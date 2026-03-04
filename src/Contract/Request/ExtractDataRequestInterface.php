<?php

namespace OneToMany\PdfPack\Contract\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;

interface ExtractDataRequestInterface extends ReadMetadataRequestInterface
{
    public const int DEFAULT_RESOLUTION = 72;
    public const int MIN_RESOLUTION = 48;
    public const int MAX_RESOLUTION = 300;

    /**
     * @return positive-int
     */
    public function getFirstPage(): int;

    /**
     * A NULL last page indicates that all pages should be extracted.
     */
    public function getLastPage(): ?int;

    public function getOutputType(): OutputType;

    /**
     * @return int<self::MIN_RESOLUTION, self::MAX_RESOLUTION>
     */
    public function getResolution(): int;
}
