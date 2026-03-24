<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\RasterizePdfRequest;
use OneToMany\PdfPack\Response\ExtractResponse;

interface ExtractActionInterface
{
    /**
     * @return \Generator<int, ExtractResponse>
     */
    public function act(RasterizePdfRequest $request): \Generator;
}
