<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Response\ConvertResponse;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;

interface ConvertActionInterface
{
    /**
     * @return \Generator<int, ConvertResponse>
     */
    public function act(ConvertRequest $convertRequest): \Generator;
}
