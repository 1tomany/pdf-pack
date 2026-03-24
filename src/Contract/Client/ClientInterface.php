<?php

namespace OneToMany\PdfPack\Contract\Client;

use OneToMany\PdfPack\Request\ConvertPdfRequest;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ExtractResponse;
use OneToMany\PdfPack\Response\ReadResponse;

interface ClientInterface
{
    public function read(ReadPdfRequest $request): ReadResponse;

    /**
     * @return \Generator<int, ExtractResponse>
     */
    public function extract(ConvertPdfRequest $request): \Generator;
}
