<?php

namespace OneToMany\PdfPack\Contract\Client;

use OneToMany\PdfPack\Contract\Request\ExtractDataRequestInterface;
use OneToMany\PdfPack\Contract\Request\ReadMetadataRequestInterface;
use OneToMany\PdfPack\Contract\Response\ExtractedDataResponseInterface;
use OneToMany\PdfPack\Contract\Response\MetadataResponseInterface;

interface ExtractorClientInterface
{
    public function readMetadata(ReadMetadataRequestInterface $request): MetadataResponseInterface;

    /**
     * @return \Generator<int, ExtractedDataResponseInterface>
     */
    public function extractData(ExtractDataRequestInterface $request): \Generator;
}
