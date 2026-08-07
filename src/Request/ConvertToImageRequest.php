<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

final class ConvertToImageRequest extends ConvertPdfRequest
{
    /**
     * @return void
     *
     * @throws InvalidArgumentException when $outputType is not an image
     */
    public function __construct(
        ?string $path,
        int $firstPage = 1,
        ?int $lastPage = null,
        OutputType $outputType = OutputType::Jpeg,
    ) {
        if ($outputType->isText()) {
            throw new InvalidArgumentException('The output type must be an image.');
        }

        parent::__construct($path, $firstPage, $lastPage, $outputType);
    }
}
