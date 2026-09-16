<?php

namespace OneToMany\PdfPack;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\PdfClientInterface;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\Files;
use OneToMany\PdfPack\Resource\Registry;

final class PdfClient implements PdfClientInterface
{
    /**
     * @var array{
     *   files: array<non-empty-lowercase-string, FilesInterface>,
     * }
     */
    private array $facades = [
        'files' => [],
    ];

    public private(set) FilesInterface $files;

    public function __construct(
        string|Vendor $vendor,
        private readonly Registry $providers,
    ) {
        $this->use($vendor);
    }

    #[\Override]
    public function use(string|Vendor $vendor): static
    {
        $vendor = Vendor::create($vendor);

        if (!isset($this->facades['files'][$vendor->value])) {
            $this->facades['files'][$vendor->value] = new Files(...[
                'provider' => $this->providers->get($vendor),
            ]);
        }

        $this->files = $this->facades['files'][$vendor->value];

        return $this;
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
        int $resolution = self::DEFAULT_RESOLUTION,
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
