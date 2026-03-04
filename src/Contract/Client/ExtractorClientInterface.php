<?php

namespace OneToMany\PdfPack\Contract\Client;

use OneToMany\PdfPack\Request\ExtractRequest;
use OneToMany\PdfPack\Request\ReadRequest;
use OneToMany\PdfPack\Response\ExtractResponse;
use OneToMany\PdfPack\Response\ReadResponse;

interface ExtractorClientInterface
{
    public function read(ReadRequest $request): ReadResponse;

    /**
     * @return \Generator<int, ExtractResponse>
     */
    public function extract(ExtractRequest $request): \Generator;
}
