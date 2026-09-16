<?php

namespace OneToMany\PdfPack\Contract;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Enum\Vendor;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Transfer\Record\PageRecord;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;

interface PdfClientInterface
{
    public FilesInterface $files { get; }

    public function use(string|Vendor $client): static;

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
