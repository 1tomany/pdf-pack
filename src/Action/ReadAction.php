<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ReadActionInterface;
use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Request\ReadRequest;
use OneToMany\PdfPack\Response\ReadResponse;

final readonly class ReadAction implements ReadActionInterface
{
    public function __construct(private ExtractorClientInterface $client)
    {
    }

    public function act(ReadRequest $request): ReadResponse
    {
        return $this->client->read($request);
    }
}
