<?php

namespace OneToMany\PdfPack\Client\Exception;

use function sprintf;

final class ExtractingDataFailedException extends BinaryProcessFailedException
{
    public function __construct(string $path, int $page, ?string $error = null, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Extracting data from page %d of the file "%s" failed.', $page, $path), $error, $previous);
    }
}
