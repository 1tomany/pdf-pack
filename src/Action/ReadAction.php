<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ReadActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;
use OneToMany\PdfPack\Transfer\Response\ReadResponse;

final readonly class ReadAction implements ReadActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ReadActionInterface
     */
    public function act(ReadRequest $readRequest): ReadResponse
    {
        return new ReadResponse($this->client->read($readRequest));
    }
}
