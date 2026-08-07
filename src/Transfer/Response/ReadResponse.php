<?php

namespace OneToMany\PdfPack\Transfer\Response;

use OneToMany\PdfPack\Contract\Transfer\Record\RecordInterface;
use OneToMany\PdfPack\Contract\Transfer\Response\ResponseInterface;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;

/**
 * @implements ResponseInterface<PdfRecord>
 */
final readonly class ReadResponse implements ResponseInterface
{
    public function __construct(
        private PdfRecord $record,
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
