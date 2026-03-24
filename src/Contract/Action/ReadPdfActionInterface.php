<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ReadPdfResponse;

interface ReadPdfActionInterface
{
    public function act(ReadPdfRequest $request): ReadPdfResponse;
}
