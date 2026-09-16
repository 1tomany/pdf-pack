<?php

namespace OneToMany\PdfPack;

use OneToMany\PdfPack\Contract\PdfClientInterface;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
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
        string|Vendor $defaultVendor,
        private readonly Registry $providers,
    ) {
        $this->use($defaultVendor);
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
}
