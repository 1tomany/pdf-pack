<?php

namespace OneToMany\PdfPack\Contract\Client;

use OneToMany\PdfPack\Contract\Enum\Vendor;
use OneToMany\PdfPack\Transfer\Record\PageRecord;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

interface ClientInterface
{
    public static function getVendor(): Vendor;

    public function read(ReadRequest $request): PdfRecord;

    /**
     * @return \Generator<int, PageRecord>
     */
    public function convert(ConvertRequest $request): \Generator;
}
