<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ConvertPdfRequest;
use OneToMany\PdfPack\Response\ConvertPdfResponse;

interface ExtractActionInterface
{
    /**
     * @return \Generator<int, ConvertPdfResponse>
     */
    public function act(ConvertPdfRequest $request): \Generator;
}
