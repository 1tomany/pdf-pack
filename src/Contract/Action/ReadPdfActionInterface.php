<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Response\ReadPdfResponse;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

interface ReadPdfActionInterface
{
    public function act(ReadRequest $readRequest): ReadPdfResponse;
}
