<?php

namespace OneToMany\PdfPack\Transfer\Record;

use OneToMany\PdfPack\Contract\Transfer\Record\RecordInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function basename;
use function trim;

final readonly class PdfRecord implements RecordInterface
{
    /**
     * @var non-empty-string
     */
    private string $path;

    /**
     * @var non-negative-int
     */
    private int $pageCount;

    /**
     * @throws InvalidArgumentException when $path is empty
     * @throws InvalidArgumentException when $pageCount is negative
     */
    public function __construct(
        string $path,
        int $pageCount,
    ) {
        if ('' === $path = trim((string) $path)) {
            throw new InvalidArgumentException('The path cannot be empty.');
        }

        $this->path = $path;

        if ($pageCount < 0) {
            throw new InvalidArgumentException('The page count cannot be negative.');
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

    public function getName(): string
    {
        return basename($this->getPath());
    }

    /**
     * @return non-negative-int
     */
    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
