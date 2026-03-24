<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Exception\InvalidArgumentException;

class BaseRequest
{
    /**
     * @var non-empty-string
     */
    private readonly string $path;

    public function __construct(?string $path)
    {
        $this->path = $this->validatePath($path);
    }

    /**
     * @return non-empty-string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return non-empty-string
     *
     * @throws InvalidArgumentException when the trimmed path is empty
     * @throws InvalidArgumentException when the file is not a readable file
     */
    private function validatePath(?string $path): string
    {
        if (!$path = trim((string) $path)) {
            throw new InvalidArgumentException('The path cannot be empty.');
        }

        if (!\is_file($path) || !\is_readable($path)) {
            throw new InvalidArgumentException(\sprintf('The file "%s" is not readable.', $path));
        }

        return $path;
    }
}
