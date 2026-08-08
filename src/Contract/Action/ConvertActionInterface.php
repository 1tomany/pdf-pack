<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Response\ConvertResponse;

interface ConvertActionInterface
{
    /**
     * @return \Generator<int, ConvertResponse>
     */
    public function act(ConvertRequest $convertRequest): \Generator;
}
