<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Response\ConvertResponse;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;

interface ConvertPdfActionInterface
{
    /**
     * @return \Generator<int, ConvertResponse>
     */
    public function act(ConvertRequest $convertRequest): \Generator;
}
