<?php

namespace OneToMany\PdfPack\Contract;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;
use OneToMany\PdfPack\Vendor;

interface PdfClientInterface
{
    public const int MIN_RESOLUTION = 48;
    public const int MAX_RESOLUTION = 300;
    public const int DEFAULT_RESOLUTION = 72;

    public FilesInterface $files { get; }

    public function use(string|Vendor $vendor): static;

    /**
     * @param positive-int $fromPage
     * @param ?positive-int $toPage
     * @param int<48,300> $resolution
     *
     * @return \Generator<int, Page>
     */
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = self::DEFAULT_RESOLUTION,
    ): \Generator;

    public function read(string $path): File;
}
