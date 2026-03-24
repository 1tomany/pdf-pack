<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ExtractPdfRequest;
use OneToMany\PdfPack\Response\ExtractResponse;

interface ExtractActionInterface
{
    /**
     * @return \Generator<int, ExtractResponse>
     */
    public function act(ExtractPdfRequest $request): \Generator;
}
