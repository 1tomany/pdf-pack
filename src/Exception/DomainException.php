<?php

namespace OneToMany\PdfPack\Exception;

use OneToMany\PdfPack\Contract\Exception\ExceptionInterface;

use function is_file;
use function is_readable;
use function sprintf;
use function strtolower;
use function trim;

class DomainException extends \DomainException implements ExceptionInterface
{
    /**
     * @return non-empty-lowercase-string
     */
    public static function validateProvider(string $provider): string
    {
        if ('' === $provider = strtolower(trim($provider))) {
            throw new self('The provider cannot be empty.');
        }

        return $provider;
    }

    /**
     * @return non-empty-string
     */
    public static function validatePath(string $path): string
    {
        if ('' === $path = trim($path)) {
            throw new self('The path cannot be empty.');
        }

        if (!is_file($path) || !is_readable($path)) {
            throw new self(sprintf('The file "%s" is not readable.', $path));
        }

        return $path;
    }

    /**
     * @return positive-int
     */
    public static function validatePage(int $page): int
    {
        if ($page < 1) {
            throw new self('The page must be greater than 0.');
        }

        return $page;
    }

    public static function validatePageRange(int $fromPage, ?int $toPage): void
    {
        self::validatePage($fromPage);

        if (null === $toPage) {
            return;
        }

        self::validatePage($toPage);

        if ($toPage < $fromPage) {
            throw new self('The ending page must be greater than or equal to the starting page.');
        }
    }

    /**
     * @return int<48, 300>
     */
    public static function validateResolution(int $resolution): int
    {
        if ($resolution < 48) {
            throw new self('The resolution must be 48 DPI or larger.');
        }

        if ($resolution > 300) {
            throw new self('The resolution must be 300 DPI or smaller.');
        }

        return $resolution;
    }
}
