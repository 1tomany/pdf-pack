<?php

namespace OneToMany\PdfPack\Contract\Resource;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Transfer\Record\PageRecord;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;

interface FilesInterface
{
    /**
     * @return \Generator<int, PageRecord>
     */
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = 72,
    ): \Generator;

    public function read(string $path): PdfRecord;
}
