<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ReadMetadataActionInterface;
use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Contract\Request\ReadMetadataRequestInterface;
use OneToMany\PdfPack\Contract\Response\MetadataResponseInterface;

final readonly class ReadMetadataAction implements ReadMetadataActionInterface
{
    public function __construct(private ExtractorClientInterface $client)
    {
    }

    public function act(ReadMetadataRequestInterface $request): MetadataResponseInterface
    {
        return $this->client->readMetadata($request);
    }
}
