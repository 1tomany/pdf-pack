<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;

class ExtractTextRequest extends ExtractDataRequest
{
    public function __construct(
        ?string $filePath,
        int $firstPage = 1,
        ?int $lastPage = 1,
        int $resolution = self::DEFAULT_RESOLUTION,
    ) {
        parent::__construct($filePath, $firstPage, $lastPage, OutputType::Text, $resolution);
    }
}
