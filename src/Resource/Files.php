<?php

namespace OneToMany\PdfPack\Resource;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

final readonly class Files implements FilesInterface
{
    public function __construct(
        private ClientInterface $client,
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
        int $resolution = ConvertRequest::DEFAULT_RESOLUTION,
    ): \Generator {
        return $this->client->convert(new ConvertRequest(
            $path,
            $fromPage,
            $toPage,
            $outputType,
            $resolution,
        ));
    }

    /**
     * @see OneToMany\PdfPack\Contract\Resource\FilesInterface
     */
    #[\Override]
    public function read(string $path): PdfRecord
    {
        return $this->client->read(new ReadRequest($path));
    }
}
