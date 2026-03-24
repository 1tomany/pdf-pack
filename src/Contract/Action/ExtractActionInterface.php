<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ConvertPdfRequest;
use OneToMany\PdfPack\Response\ExtractResponse;

interface ExtractActionInterface
{
    /**
     * @return \Generator<int, ExtractResponse>
     */
    public function act(ConvertPdfRequest $request): \Generator;
}
