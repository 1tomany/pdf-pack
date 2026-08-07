<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Response\ReadPdfResponse;
use OneToMany\PdfPack\Transfer\Request\ReadPdfRequest;

interface ReadPdfActionInterface
{
    public function act(ReadPdfRequest $readPdfRequest): ReadPdfResponse;
}
