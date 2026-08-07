<?php

namespace OneToMany\PdfPack\Contract\Transfer\Response;

use OneToMany\PdfPack\Contract\Transfer\Record\RecordInterface;

/**
 * @template R of RecordInterface
 */
interface ResponseInterface
{
    /**
     * @return R
     */
    public function __invoke(): RecordInterface;

    /**
     * @return R
     */
    public function getRecord(): RecordInterface;
}
