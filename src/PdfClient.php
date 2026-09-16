<?php

namespace OneToMany\PdfPack;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\PdfClientInterface;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\Files;
use OneToMany\PdfPack\Resource\Registry;

final readonly class PdfClient implements PdfClientInterface
{
    public FilesInterface $files;

    public function __construct(
        private Registry $providers,
        string|Vendor $vendor = Vendor::Poppler,
    ) {
        $this->files = new Files($this->providers->get(Vendor::create($vendor)));
    }

    #[\Override]
    public function use(string|Vendor $vendor): static
    {
        return new self($this->providers, $vendor);
    }

    /**
     * Convenience alias for $this->files->convert().
     *
     * @see OneToMany\PdfPack\Contract\PdfClientInterface
     */
    #[\Override]
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = 72,
    ): \Generator {
        return $this->files->convert($path, $fromPage, $toPage, $outputType, $resolution);
    }

    /**
     * Convenience alias for $this->files->read().
     *
     * @see OneToMany\PdfPack\Contract\PdfClientInterface
     */
    #[\Override]
    public function read(string $path): File
    {
        return $this->files->read($path);
    }
}
