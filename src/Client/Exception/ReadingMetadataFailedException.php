<?php

namespace OneToMany\PdfPack\Client\Exception;

use function sprintf;

final class ReadingMetadataFailedException extends BinaryProcessFailedException
{
    public function __construct(string $filePath, ?string $error = null, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Reading the metadata from the file "%s" failed.', $filePath), $error, $previous);
    }
}
