<?php

namespace OneToMany\PdfPack\Response;

use function max;

final readonly class ReadResponse
{
    /**
     * @param positive-int $pages
     */
    public function __construct(private int $pages = 1)
    {
    }

    /**
     * @return positive-int
     */
    public function getPages(): int
    {
        return max(1, $this->pages);
    }
}
