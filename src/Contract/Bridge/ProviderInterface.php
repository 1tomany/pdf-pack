<?php

namespace OneToMany\PdfPack\Contract\Bridge;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;
use OneToMany\PdfPack\Vendor;

interface ProviderInterface
{
    public static function getVendor(): Vendor;

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
