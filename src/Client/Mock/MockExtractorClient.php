<?php

namespace OneToMany\PdfPack\Client\Mock;

use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Contract\Request\ExtractDataRequestInterface;
use OneToMany\PdfPack\Contract\Request\ReadMetadataRequestInterface;
use OneToMany\PdfPack\Contract\Response\MetadataResponseInterface;
use OneToMany\PdfPack\Exception\RuntimeException;
use OneToMany\PdfPack\Response\ReadResponse;

use function random_int;

readonly class MockExtractorClient implements ExtractorClientInterface
{
    public function __construct()
    {
    }

    public function readMetadata(ReadMetadataRequestInterface $request): MetadataResponseInterface
    {
        return new ReadResponse(random_int(1, 100));
    }

    public function extractData(ExtractDataRequestInterface $request): \Generator
    {
        throw new RuntimeException('Not implemented!');
    }
}
