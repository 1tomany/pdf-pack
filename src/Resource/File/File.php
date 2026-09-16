<?php

namespace OneToMany\PdfPack\Resource\File;

use OneToMany\PdfPack\Exception\DomainException;

use function basename;
use function trim;

final readonly class File
{
    /**
     * @var non-empty-string
     */
    public string $path;

    /**
     * @var non-empty-string
     */
    public string $name;

    /**
     * @var non-negative-int
     */
    public int $pageCount;

    public function __construct(string $path, int $pageCount)
    {
        if ('' === $path = trim($path)) {
            throw new DomainException('The path cannot be empty.');
        }

        $this->path = $path;

        if ('' === $name = basename($path)) {
            throw new DomainException('The file name cannot be empty.');
        }

        $this->name = $name;

        if ($pageCount < 0) {
            throw new DomainException('The page count cannot be negative.');
        }

        $this->pageCount = $pageCount;
    }

    /**
     * @return non-empty-string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-negative-int
     */
    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
