<?php

namespace OneToMany\PdfPack\Contract;

use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Vendor;

interface PdfClientInterface
{
    public FilesInterface $files { get; }

    public function use(string|Vendor $vendor): static;
}
