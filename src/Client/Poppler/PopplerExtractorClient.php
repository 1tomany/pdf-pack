<?php

namespace OneToMany\PdfPack\Client\Poppler;

use OneToMany\PdfPack\Client\Exception\ExtractingDataFailedException;
use OneToMany\PdfPack\Client\Exception\ReadingMetadataFailedException;
use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Contract\Request\ExtractDataRequestInterface;
use OneToMany\PdfPack\Contract\Request\ReadMetadataRequestInterface;
use OneToMany\PdfPack\Contract\Response\MetadataResponseInterface;
use OneToMany\PdfPack\Helper\BinaryFinder;
use OneToMany\PdfPack\Request\ReadRequest;
use OneToMany\PdfPack\Response\ExtractedDataResponse;
use OneToMany\PdfPack\Response\MetadataResponse;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

use function explode;
use function str_contains;
use function strcmp;

readonly class PopplerExtractorClient implements ExtractorClientInterface
{
    public function __construct(
        private string $pdfInfoBinary = 'pdfinfo',
        private string $pdfToPpmBinary = 'pdftoppm',
        private string $pdfToTextBinary = 'pdftotext',
    ) {
    }

    public function readMetadata(ReadMetadataRequestInterface $request): MetadataResponseInterface
    {
        $process = new Process([BinaryFinder::find($this->pdfInfoBinary), $request->getFilePath()]);

        try {
            $output = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new ReadingMetadataFailedException($request->getFilePath(), $process->getErrorOutput(), $e);
        }

        $response = new MetadataResponse();

        foreach (explode("\n", $output) as $infoBit) {
            if (str_contains($infoBit, ':')) {
                $bits = explode(':', $infoBit);

                if (0 === strcmp('Pages', $bits[0])) {
                    $response->setPages((int) $bits[1]);
                }
            }
        }

        return $response;
    }

    public function extractData(ExtractDataRequestInterface $request): \Generator
    {
        // Determine the number of pages to extract
        if (null === $lastPage = $request->getLastPage()) {
            $metadataRequest = new ReadRequest(...[
                'filePath' => $request->getFilePath(),
            ]);

            $lastPage = $this->readMetadata($metadataRequest)->getPages();
        }

        if ($request->getOutputType()->isText()) {
            $command = BinaryFinder::find($this->pdfToTextBinary);

            for ($page = $request->getFirstPage(); $page <= $lastPage; ++$page) {
                $process = new Process([$command, '-nodiag', '-f', $page, '-l', $page, '-r', $request->getResolution(), $request->getFilePath(), '-']);

                try {
                    $output = $process->mustRun()->getOutput();
                } catch (ProcessExceptionInterface $e) {
                    throw new ExtractingDataFailedException($request->getFilePath(), $page, $process->getErrorOutput(), $e);
                }

                yield new ExtractedDataResponse($request->getOutputType(), $output, $page);
            }
        } else {
            $command = BinaryFinder::find($this->pdfToPpmBinary);

            for ($page = $request->getFirstPage(); $page <= $lastPage; ++$page) {
                $process = new Process([$command, $request->getOutputType()->isJpeg() ? '-jpeg' : '-png', '-f', $page, '-l', $page, '-r', $request->getResolution(), $request->getFilePath()]);

                try {
                    $output = $process->mustRun()->getOutput();
                } catch (ProcessExceptionInterface $e) {
                    throw new ExtractingDataFailedException($request->getFilePath(), $page, $process->getErrorOutput(), $e);
                }

                yield new ExtractedDataResponse($request->getOutputType(), $output, $page);
            }
        }
    }
}
