<?php

namespace OneToMany\PdfPack\Client\Mock;

use OneToMany\PdfPack\Contract\Client\ExtractorClientInterface;
use OneToMany\PdfPack\Exception\RuntimeException;
use OneToMany\PdfPack\Request\ExtractRequest;
use OneToMany\PdfPack\Request\ReadRequest;
use OneToMany\PdfPack\Response\ReadResponse;

use function random_int;

readonly class MockExtractorClient implements ExtractorClientInterface
{
    public function __construct()
    {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ExtractorClientInterface
     */
    public function read(ReadRequest $request): ReadResponse
    {
        return new ReadResponse(random_int(1, 100));
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ExtractorClientInterface
     */
    public function extract(ExtractRequest $request): \Generator
    {
        throw new RuntimeException('Not implemented!');
    }
}
