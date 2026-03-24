<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ReadActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ReadPdfResponse;

final readonly class ReadPdfAction implements ReadActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ReadActionInterface
     */
    public function act(ReadPdfRequest $request): ReadPdfResponse
    {
        return $this->client->read($request);
    }
}
