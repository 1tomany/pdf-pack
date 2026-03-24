<?php

namespace OneToMany\PdfPack\Client\Exception;

use function sprintf;

final class ReadingPdfFailedException extends BinaryProcessFailedException
{
    public function __construct(string $path, ?string $error = null, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Reading the file "%s" failed.', $path), $error, $previous);
    }
}
