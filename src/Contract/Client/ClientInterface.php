<?php

namespace OneToMany\PdfPack\Contract\Client;

use OneToMany\PdfPack\Request\ConvertPdfRequest;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ConvertPdfResponse;
use OneToMany\PdfPack\Response\ReadPdfResponse;

interface ClientInterface
{
    public function read(ReadPdfRequest $request): ReadPdfResponse;

    /**
     * @return \Generator<int, ConvertPdfResponse>
     */
    public function extract(ConvertPdfRequest $request): \Generator;
}
