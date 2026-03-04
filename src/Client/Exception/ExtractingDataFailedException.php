<?php

namespace OneToMany\PdfPack\Client\Exception;

use function sprintf;

final class ExtractingDataFailedException extends BinaryProcessFailedException
{
    public function __construct(string $filePath, int $pageNumber, ?string $error = null, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Extracting data from page %d of the file "%s" failed.', $pageNumber, $filePath), $error, $previous);
    }
}
