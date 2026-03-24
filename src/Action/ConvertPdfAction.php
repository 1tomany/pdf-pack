<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ConvertPdfActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Request\ConvertPdfRequest;

final readonly class ConvertPdfAction implements ConvertPdfActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ConvertPdfActionInterface
     */
    public function act(ConvertPdfRequest $request): \Generator
    {
        return $this->client->convert($request);
    }
}
