<?php

namespace OneToMany\PdfPack\Client\Poppler;

use OneToMany\PdfPack\Client\Exception\ConvertingPdfFailedException;
use OneToMany\PdfPack\Client\Exception\ReadingPdfFailedException;
use OneToMany\PdfPack\Client\Service\BinaryFinder;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Response\ConvertResponse;
use OneToMany\PdfPack\Response\ReadPdfResponse;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;
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
    #[\Override]
    public static function getVendor(): string
    {
        return 'poppler';
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     */
    #[\Override]
    public function read(ReadRequest $request): PdfRecord
    {
        $process = new Process([BinaryFinder::find($this->pdfInfoBinary), $request->getPath()]);

        try {
            $output = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new ReadingPdfFailedException($request->getPath(), $process->getErrorOutput(), $e);
        }

        foreach (explode("\n", $output) as $infoBit) {
            if (true === str_contains($infoBit, ':')) {
                $infoBits = explode(':', $infoBit);

                if ('Pages' === $infoBits[0]) {
                    $pageCount = (int) $infoBits[1];
                }
            }
        }

        return new PdfRecord($request->getPath(), $pageCount ?? 1);
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     *
     * @throws ConvertingPdfFailedException when converting one or more pages of a PDF to an image fails
     */
    #[\Override]
    public function convert(ConvertRequest $request): \Generator
    {
        // Determine the number of pages to extract
        if (!$lastPage = $request->getLastPage()) {
            $readRequest = new ReadRequest(...[
                'path' => $request->getPath(),
            ]);

            $lastPage = $this->read($readRequest)->getPages();
        }

        if ($request->getOutputType()->isText()) {
            $command = BinaryFinder::find($this->pdfToTextBinary);

            for ($page = $request->getFirstPage(); $page <= $lastPage; ++$page) {
                $process = new Process([$command, '-nodiag', '-f', (string) $page, '-l', (string) $page, '-r', (string) $request->getResolution(), $request->getPath(), '-']);

                try {
                    $output = $process->mustRun()->getOutput();
                } catch (ProcessExceptionInterface $e) {
                    throw new ConvertingPdfFailedException($request->getPath(), $page, $process->getErrorOutput(), $e);
                }

                yield new ConvertResponse($request->getOutputType(), $output, $page);
            }
        } else {
            $command = BinaryFinder::find($this->pdfToPpmBinary);

            for ($page = $request->getFirstPage(); $page <= $lastPage; ++$page) {
                $process = new Process([$command, $request->getOutputType()->isJpeg() ? '-jpeg' : '-png', '-f', $page, '-l', $page, '-r', $request->getResolution(), $request->getPath()]);

                try {
                    $output = $process->mustRun()->getOutput();
                } catch (ProcessExceptionInterface $e) {
                    throw new ConvertingPdfFailedException($request->getPath(), $page, $process->getErrorOutput(), $e);
                }

                yield new ConvertResponse($request->getOutputType(), $output, $page);
            }
        }
    }
}
