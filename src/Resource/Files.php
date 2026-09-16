<?php

namespace OneToMany\PdfPack\Resource;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Resource\File\File;

final readonly class Files implements FilesInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Resource\FilesInterface
     */
    #[\Override]
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = 72,
    ): \Generator {
        $path = DomainException::validatePath($path);
        DomainException::validatePageRange($fromPage, $toPage);
        $resolution = DomainException::validateResolution($resolution);

        return $this->provider->convert(
            $path,
            $fromPage,
            $toPage,
            $outputType,
            $resolution,
        );
    }

    /**
     * @see OneToMany\PdfPack\Contract\Resource\FilesInterface
     */
    #[\Override]
    public function read(string $path): File
    {
        return $this->provider->read(DomainException::validatePath($path));
    }
}
