<?php

namespace OneToMany\PdfPack\Contract\Client;

use OneToMany\PdfPack\Response\ConvertPdfResponse;
use OneToMany\PdfPack\Response\ReadPdfResponse;
use OneToMany\PdfPack\Transfer\Request\ConvertPdfRequest;
use OneToMany\PdfPack\Transfer\Request\ReadPdfRequest;

interface ClientInterface
{
    /**
     * @return non-empty-lowercase-string
     */
    public static function getVendor(): string;

    public function read(ReadPdfRequest $request): ReadPdfResponse;

    /**
     * @return \Generator<int, ConvertPdfResponse>
     */
    public function convert(ConvertPdfRequest $request): \Generator;
}
