<?php

namespace OneToMany\PdfPack\Contract\Action;

use OneToMany\PdfPack\Contract\Request\ReadMetadataRequestInterface;
use OneToMany\PdfPack\Contract\Response\MetadataResponseInterface;

interface ReadMetadataActionInterface
{
    public function act(ReadMetadataRequestInterface $request): MetadataResponseInterface;
}
