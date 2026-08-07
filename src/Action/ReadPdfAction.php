<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ReadPdfActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Response\ReadPdfResponse;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

final readonly class ReadPdfAction implements ReadPdfActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ReadPdfActionInterface
     */
    public function act(ReadRequest $readRequest): ReadPdfResponse
    {
        return $this->client->read($readRequest);
    }
}
