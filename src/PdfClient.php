<?php

namespace OneToMany\PdfPack;

use OneToMany\PdfPack\Contract\PdfClientInterface;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Exception\DomainException;
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
        string $defaultProvider,
        private readonly Registry $providers,
    ) {
        $this->use($defaultProvider);
    }

    #[\Override]
    public function use(string $provider): static
    {
        $provider = DomainException::validateProvider($provider);

        if (!isset($this->facades['files'][$provider])) {
            $this->facades['files'][$provider] = new Files(...[
                'provider' => $this->providers->get($provider),
            ]);
        }

        $this->files = $this->facades['files'][$provider];

        return $this;
    }
}
