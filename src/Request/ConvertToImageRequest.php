<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

final class ConvertToImageRequest extends ConvertPdfRequest
{
    public function __construct(
        ?string $path,
        int $firstPage = 1,
        ?int $lastPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = self::DEFAULT_RESOLUTION,
    ) {
        if ($outputType->isText()) {
            throw new InvalidArgumentException('The output type must be an image.');
        }

        parent::__construct($path, $firstPage, $lastPage, $outputType, $resolution);
    }
}
