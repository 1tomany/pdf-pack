<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ExtractActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Request\RasterizePdfRequest;

final readonly class ExtractPdfAction implements ExtractActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ExtractActionInterface
     */
    public function act(RasterizePdfRequest $request): \Generator
    {
        return $this->client->extract($request);
    }
}
