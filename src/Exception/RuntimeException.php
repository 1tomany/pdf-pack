<?php

namespace OneToMany\PdfPack\Exception;

use OneToMany\PdfPack\Contract\Exception\ExceptionInterface;

use function explode;
use function rtrim;
use function sprintf;
use function trim;

class RuntimeException extends \RuntimeException implements ExceptionInterface
{
    public static function binaryProcessFailed(
        string $message,
        ?string $error = null,
        ?\Throwable $previous = null,
    ): self {
        $error = trim(explode("\n", $error ?? '')[0]) ?: null;

        return new self(null === $error ? $message : sprintf('%s: %s.', rtrim($message, '.'), rtrim($error, '.')), previous: $previous);
    }

    public static function convertingPdfFailed(
        string $path,
        int $page,
        ?string $error = null,
        ?\Throwable $previous = null,
    ): self {
        return self::binaryProcessFailed(sprintf('Converting page %d of the file "%s" failed.', $page, $path), $error, $previous);
    }

    public static function readingPdfFailed(
        string $path,
        ?string $error = null,
        ?\Throwable $previous = null,
    ): self {
        return self::binaryProcessFailed(sprintf('Reading the file "%s" failed.', $path), $error, $previous);
    }
}
