<?php

namespace OneToMany\PdfPack\Action;

use OneToMany\PdfPack\Contract\Action\ConvertActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Response\ConvertResponse;

final readonly class ConvertAction implements ConvertActionInterface
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Action\ConvertActionInterface
     */
    public function act(ConvertRequest $convertRequest): \Generator
    {
        yield new ConvertResponse($this->client->convert($convertRequest));
    }
}
