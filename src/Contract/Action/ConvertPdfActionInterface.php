<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Response\ConvertPdfResponse;
use OneToMany\PdfPack\Transfer\Request\ConvertPdfRequest;

interface ConvertPdfActionInterface
{
    /**
     * @return \Generator<int, ConvertPdfResponse>
     */
    public function act(ConvertPdfRequest $convertPdfRequest): \Generator;
}
