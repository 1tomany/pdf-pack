<?php

namespace OneToMany\PdfPack\Contract\Resource;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;

interface FilesInterface
{
    /**
     * @return \Generator<int, Page>
     */
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = 72,
    ): \Generator;

    public function read(string $path): File;
}
