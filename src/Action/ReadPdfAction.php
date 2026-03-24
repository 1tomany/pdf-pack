<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ReadPdfActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ReadPdfResponse;

final readonly class ReadPdfAction implements ReadPdfActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ReadPdfActionInterface
     */
    public function act(ReadPdfRequest $request): ReadPdfResponse
    {
        return $this->client->read($request);
    }
}
