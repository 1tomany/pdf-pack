<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Transfer\Request\ReadRequest;
use OneToMany\PdfPack\Transfer\Response\ReadResponse;

interface ReadActionInterface
{
    public function act(ReadRequest $readRequest): ReadResponse;
}
