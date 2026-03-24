<?php

namespace OneToMany\PdfPack\Response;

use function basename;
use function max;

final readonly class ReadPdfResponse
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        private string $path,
        private int $pages,
    ) {
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
     * @return positive-int
     */
    public function getPages(): int
    {
        return max(1, $this->pages);
    }
}
