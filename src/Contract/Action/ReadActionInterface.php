<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ReadPdfResponse;

interface ReadActionInterface
{
    public function act(ReadPdfRequest $request): ReadPdfResponse;
}
