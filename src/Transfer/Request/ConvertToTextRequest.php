<?php

namespace OneToMany\PdfPack\Transfer\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;

final class ConvertToTextRequest extends ConvertRequest
{
    public function __construct(
        ?string $path,
        int $firstPage = 1,
        ?int $lastPage = null,
    ) {
        parent::__construct($path, $firstPage, $lastPage, OutputType::Text);
    }
}
