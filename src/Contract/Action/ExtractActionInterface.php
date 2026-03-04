<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ExtractRequest;
use OneToMany\PdfPack\Response\ExtractResponse;

interface ExtractActionInterface
{
    /**
     * @return \Generator<int, ExtractResponse>
     */
    public function act(ExtractRequest $request): \Generator;
}
