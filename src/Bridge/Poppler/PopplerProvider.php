<?php

namespace OneToMany\PdfPack\Bridge\Poppler;

use OneToMany\PdfPack\Bridge\Trait\BinaryTrait;
use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Exception\RuntimeException;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

use function explode;
use function str_starts_with;
use function substr;
use function trim;

final readonly class PopplerProvider implements ProviderInterface
{
    use BinaryTrait;

    public function __construct(
        private string $pdfInfoBinary = 'pdfinfo',
        private string $pdfToPpmBinary = 'pdftoppm',
        private string $pdfToTextBinary = 'pdftotext',
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Bridge\ProviderInterface
     *
     * @return 'poppler'
     */
    #[\Override]
    public static function getProvider(): string
    {
        return 'poppler';
    }

    /**
     * @see OneToMany\PdfPack\Contract\Bridge\ProviderInterface
     *
     * @throws RuntimeException when converting one or more pages fails
     */
    #[\Override]
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = FilesInterface::DEFAULT_RESOLUTION,
    ): \Generator {
        $toPage ??= $this->read($path)->getPageCount();

        if ($outputType->isText()) {
            $binary = $this->findBinary($this->pdfToTextBinary);

            for ($page = $fromPage; $page <= $toPage; ++$page) {
                $process = new Process([$binary, '-nodiag', '-f', (string) $page, '-l', (string) $page, '-r', (string) $resolution, $path, '-']);

                try {
                    $output = $process->mustRun()->getOutput();
                } catch (ProcessExceptionInterface $e) {
                    throw RuntimeException::convertingPdfFailed($path, $page, $process->getErrorOutput(), $e);
                }

                yield new Page($outputType, $output, $page);
            }

            return;
        }

        $binary = $this->findBinary($this->pdfToPpmBinary);

        for ($page = $fromPage; $page <= $toPage; ++$page) {
            $process = new Process([$binary, $outputType->isJpeg() ? '-jpeg' : '-png', '-f', (string) $page, '-l', (string) $page, '-r', (string) $resolution, $path]);

            try {
                $output = $process->mustRun()->getOutput();
            } catch (ProcessExceptionInterface $e) {
                throw RuntimeException::convertingPdfFailed($path, $page, $process->getErrorOutput(), $e);
            }

            yield new Page($outputType, $output, $page);
        }
    }

    /**
     * @see OneToMany\PdfPack\Contract\Bridge\ProviderInterface
     *
     * @throws RuntimeException when reading the PDF fails
     */
    #[\Override]
    public function read(string $path): File
    {
        $process = new Process([$this->findBinary($this->pdfInfoBinary), $path]);

        try {
            $output = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw RuntimeException::readingPdfFailed($path, $process->getErrorOutput(), $e);
        }

        foreach (explode("\n", $output) as $line) {
            if (str_starts_with($line, 'Pages:')) {
                $pageCount = trim(substr($line, 6));
            }
        }

        return new File($path, isset($pageCount) ? (int) $pageCount : 1);
    }
}
