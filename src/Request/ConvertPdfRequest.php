<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;

class ConvertPdfRequest extends ExtractPdfRequest
{
    public function __construct(
        ?string $path,
        int $firstPage = 1,
        ?int $lastPage = null,
        OutputType $outputType,
        int $resolution = self::DEFAULT_RESOLUTION,
    ) {
        parent::__construct($path, $firstPage, $lastPage, $outputType, $resolution);
    }
}
