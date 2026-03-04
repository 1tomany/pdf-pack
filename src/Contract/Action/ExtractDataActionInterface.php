<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Contract\Request\ExtractDataRequestInterface;
use OneToMany\PdfPack\Contract\Response\ExtractedDataResponseInterface;

interface ExtractDataActionInterface
{
    /**
     * @return \Generator<int, ExtractedDataResponseInterface>
     */
    public function act(ExtractDataRequestInterface $request): \Generator;
}
