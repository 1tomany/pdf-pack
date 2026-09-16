<?php

namespace OneToMany\PdfPack\Contract\Resource;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;

interface FilesInterface
{
    public const int MIN_RESOLUTION = 48;
    public const int MAX_RESOLUTION = 300;
    public const int DEFAULT_RESOLUTION = 72;

    /**
     * @param positive-int $firstPage
     * @param ?positive-int $lastPage
     * @param int<self::MIN_RESOLUTION, self::MAX_RESOLUTION> $resolution
     *
     * @return \Generator<int, Page>
     *
     * @throws DomainException when the path is empty
     * @throws DomainException when the path is not a readable file
     * @throws DomainException when the first page is less than 1
     * @throws DomainException when the last page is less than 1
     * @throws DomainException when the last page is less than the first page
     * @throws \RangeException when the resolution is outside the minimum and maximum bounds
     */
    public function convert(
        string $path,
        int $firstPage = 1,
        ?int $lastPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = self::DEFAULT_RESOLUTION,
    ): \Generator;

    public function read(string $path): File;
}
