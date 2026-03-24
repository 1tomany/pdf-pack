<?php

namespace OneToMany\PdfPack\Client\Mock;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Exception\RuntimeException;
use OneToMany\PdfPack\Request\ConvertPdfRequest;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ReadPdfResponse;

use function random_int;

readonly class MockClient implements ClientInterface
{
    public function __construct()
    {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     */
    public static function getVendor(): string
    {
        return 'mock';
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     */
    public function read(ReadPdfRequest $request): ReadPdfResponse
    {
        return new ReadPdfResponse(random_int(1, 100));
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     */
    public function extract(ConvertPdfRequest $request): \Generator
    {
        throw new RuntimeException('Not implemented!');
    }
}
