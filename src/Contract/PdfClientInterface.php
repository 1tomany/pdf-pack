<?php

namespace OneToMany\PdfPack\Contract;

use OneToMany\PdfPack\Contract\Resource\FilesInterface;

interface PdfClientInterface
{
    public FilesInterface $files { get; }

    public function use(string $provider): static;
}
