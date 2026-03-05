<?php

namespace OneToMany\PdfPack\Response;

use function max;

final readonly class ReadResponse
{
    /** @var positive-int */
    private int $pages;

    public function __construct(int $pages = 1)
    {
        $this->pages = max(1, $pages);
    }

    /**
     * @return positive-int
     */
    public function getPages(): int
    {
        return $this->pages;
    }
}
