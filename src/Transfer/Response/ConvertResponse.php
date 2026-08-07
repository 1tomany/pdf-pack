<?php

namespace OneToMany\PdfPack\Transfer\Response;

use OneToMany\PdfPack\Contract\Transfer\Record\RecordInterface;
use OneToMany\PdfPack\Contract\Transfer\Response\ResponseInterface;
use OneToMany\PdfPack\Transfer\Record\PageRecord;

/**
 * @implements ResponseInterface<PageRecord>
 */
final readonly class ConvertResponse implements ResponseInterface
{
    public function __construct(
        private PageRecord $record,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Transfer\Response\ResponseInterface
     */
    #[\Override]
    public function __invoke(): RecordInterface
    {
        return $this->record;
    }

    /**
     * @see OneToMany\PdfPack\Contract\Transfer\Response\ResponseInterface
     */
    #[\Override]
    public function getRecord(): RecordInterface
    {
        return $this->record;
    }
}
