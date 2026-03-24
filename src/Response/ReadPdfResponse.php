<?php

namespace OneToMany\PdfPack\Response;

use function max;

final readonly class ReadPdfResponse
{
    /**
     * @param positive-int $pages
     */
    public function __construct(
        private int $pages,
    ) {
    }

    /**
     * @return positive-int
     */
    public function getPages(): int
    {
        return max(1, $this->pages);
    }
}
