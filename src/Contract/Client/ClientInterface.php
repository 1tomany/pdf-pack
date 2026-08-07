<?php

namespace OneToMany\PdfPack\Contract\Client;

use OneToMany\PdfPack\Response\ConvertResponse;
use OneToMany\PdfPack\Response\ReadPdfResponse;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

interface ClientInterface
{
    /**
     * @return non-empty-lowercase-string
     */
    public static function getVendor(): string;

    public function read(ReadRequest $request): ReadPdfResponse;

    /**
     * @return \Generator<int, ConvertResponse>
     */
    public function convert(ConvertRequest $request): \Generator;
}
