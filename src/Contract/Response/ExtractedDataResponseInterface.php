<?php

namespace OneToMany\PdfPack\Contract\Response;

use OneToMany\PdfPack\Contract\Enum\OutputType;

interface ExtractedDataResponseInterface extends \Stringable
{
    public function getType(): OutputType;

    public function getData(): string;

    /**
     * @return positive-int
     */
    public function getPage(): int;

    /**
     * @return non-empty-string
     */
    public function getName(): string;

    public function toDataUri(): string;
}
