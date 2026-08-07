<?php

namespace OneToMany\PdfPack\Client\Mock;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Exception\RuntimeException;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

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
    public function read(ReadRequest $request): PdfRecord
    {
        return new PdfRecord($request->getPath(), random_int(1, 100));
    }

    /**
     * @see OneToMany\PdfPack\Contract\Client\ClientInterface
     */
    public function convert(ConvertRequest $request): \Generator
    {
        throw new RuntimeException('Not implemented!');
    }
}
