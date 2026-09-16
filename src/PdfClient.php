<?php

namespace OneToMany\PdfPack;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Enum\Vendor;
use OneToMany\PdfPack\Contract\PdfClientInterface;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Factory\ClientFactory;
use OneToMany\PdfPack\Resource\Files;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;

final readonly class PdfClient implements PdfClientInterface
{
    public FilesInterface $files;

    public function __construct(
        private ClientFactory $clientFactory,
        string|Vendor $client = Vendor::Poppler,
    ) {
        $this->files = new Files($this->clientFactory->create($client));
    }

    #[\Override]
    public function use(string|Vendor $client): static
    {
        return new self($this->clientFactory, $client);
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
        int $resolution = ConvertRequest::DEFAULT_RESOLUTION,
    ): \Generator {
        return $this->files->convert($path, $fromPage, $toPage, $outputType, $resolution);
    }

    /**
     * Convenience alias for $this->files->read().
     *
     * @see OneToMany\PdfPack\Contract\PdfClientInterface
     */
    #[\Override]
    public function read(string $path): PdfRecord
    {
        return $this->files->read($path);
    }
}
