<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Request\ReadRequest;
use OneToMany\PdfPack\Response\ReadResponse;

interface ReadActionInterface
{
    public function act(ReadRequest $request): ReadResponse;
}
