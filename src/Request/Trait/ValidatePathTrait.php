<?php

namespace OneToMany\PdfPack\Request\Trait;

use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function is_file;
use function is_readable;
use function sprintf;
use function trim;

trait ValidatePathTrait
{
    /**
     * @return non-empty-string
     *
     * @throws InvalidArgumentException when the trimmed path is empty
     * @throws InvalidArgumentException when the path is not a readable file
     */
    private function validatePath(string $path): string
    {
        $path = trim($path ?? '');

        if ('' === $path) {
            throw new InvalidArgumentException('The path cannot be empty.');
        }

        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('The file "%s" is not readable.', $path));
        }

        return $path;
    }
}
