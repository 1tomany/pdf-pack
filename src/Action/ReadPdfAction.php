<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ReadPdfActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Response\ReadPdfResponse;
use OneToMany\PdfPack\Transfer\Request\ReadPdfRequest;

final readonly class ReadPdfAction implements ReadPdfActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ReadPdfActionInterface
     */
    public function act(ReadPdfRequest $readPdfRequest): ReadPdfResponse
    {
        return $this->client->read($readPdfRequest);
    }
}
