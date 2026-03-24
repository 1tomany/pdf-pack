<?php

namespace OneToMany\PdfPack\Client\Poppler;

use OneToMany\PdfPack\Client\Exception\ExtractingDataFailedException;
use OneToMany\PdfPack\Client\Exception\ReadingFileFailedException;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Helper\BinaryFinder;
use OneToMany\PdfPack\Request\RasterizePdfRequest;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ExtractResponse;
use OneToMany\PdfPack\Response\ReadResponse;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

use function explode;
use function str_contains;

final readonly class PopplerClient implements ClientInterface
{
    public function __construct(
        private string $pdfInfoBinary = 'pdfinfo',
        private string $pdfToPpmBinary = 'pdftoppm',
        private string $pdfToTextBinary = 'pdftotext',
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     */
    public function read(ReadPdfRequest $request): ReadResponse
    {
        $process = new Process([BinaryFinder::find($this->pdfInfoBinary), $request->getPath()]);

        try {
            $output = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new ReadingFileFailedException($request->getPath(), $process->getErrorOutput(), $e);
        }

        foreach (explode("\n", $output) as $infoBit) {
            if (str_contains($infoBit, ':')) {
                $bits = explode(':', $infoBit);

                if ('Pages' === $bits[0]) {
                    $pages = (int) $bits[1];
                }
            }
        }

        return new ReadResponse($pages ?? 1);
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     */
    public function extract(RasterizePdfRequest $request): \Generator
    {
        // Determine the number of pages to extract
        if (!$lastPage = $request->getLastPage()) {
            $readRequest = new ReadPdfRequest(...[
                'path' => $request->getPath(),
            ]);

            $lastPage = $this->read($readRequest)->getPages();
        }

        if ($request->getOutputType()->isText()) {
            $command = BinaryFinder::find($this->pdfToTextBinary);

            for ($page = $request->getFirstPage(); $page <= $lastPage; ++$page) {
                $process = new Process([$command, '-nodiag', '-f', $page, '-l', $page, '-r', $request->getResolution(), $request->getPath(), '-']);

                try {
                    $output = $process->mustRun()->getOutput();
                } catch (ProcessExceptionInterface $e) {
                    throw new ExtractingDataFailedException($request->getPath(), $page, $process->getErrorOutput(), $e);
                }

                yield new ExtractResponse($request->getOutputType(), $output, $page);
            }
        } else {
            $command = BinaryFinder::find($this->pdfToPpmBinary);

            for ($page = $request->getFirstPage(); $page <= $lastPage; ++$page) {
                $process = new Process([$command, $request->getOutputType()->isJpeg() ? '-jpeg' : '-png', '-f', $page, '-l', $page, '-r', $request->getResolution(), $request->getPath()]);

                try {
                    $output = $process->mustRun()->getOutput();
                } catch (ProcessExceptionInterface $e) {
                    throw new ExtractingDataFailedException($request->getPath(), $page, $process->getErrorOutput(), $e);
                }

                yield new ExtractResponse($request->getOutputType(), $output, $page);
            }
        }
    }
}
