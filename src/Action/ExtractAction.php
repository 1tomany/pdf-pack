<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ExtractActionInterface;
use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Request\ExtractRequest;

final readonly class ExtractAction implements ExtractActionInterface
{
    public function __construct(private ExtractorClientInterface $client)
    {
    }

    public function act(ExtractRequest $request): \Generator
    {
        return $this->client->extract($request);
    }
}
