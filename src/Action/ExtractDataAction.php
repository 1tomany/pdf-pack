<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ExtractDataActionInterface;
use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Contract\Request\ExtractDataRequestInterface;

final readonly class ExtractDataAction implements ExtractDataActionInterface
{
    public function __construct(private ExtractorClientInterface $client)
    {
    }

    public function act(ExtractDataRequestInterface $request): \Generator
    {
        return $this->client->extractData($request);
    }
}
