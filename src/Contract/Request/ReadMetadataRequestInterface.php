<?php

namespace OneToMany\PdfPack\Contract\Request;

interface ReadMetadataRequestInterface
{
    /**
     * @return non-empty-string
     */
    public function getFilePath(): string;
}
