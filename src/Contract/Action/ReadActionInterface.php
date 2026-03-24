<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ReadResponse;

interface ReadActionInterface
{
    public function act(ReadPdfRequest $request): ReadResponse;
}
