<?php

namespace OneToMany\PdfPack\Contract\Response;

interface MetadataResponseInterface
{
    /**
     * @return positive-int
     */
    public function getPages(): int;
}
