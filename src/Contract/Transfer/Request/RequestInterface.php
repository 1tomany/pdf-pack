<?php

namespace OneToMany\PdfPack\Contract\Transfer\Request;

interface RequestInterface
{
    /**
     * @return non-empty-string
     */
    public function getPath(): string;
}
