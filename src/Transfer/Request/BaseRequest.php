<?php

namespace OneToMany\PdfPack\Transfer\Request;

use OneToMany\PdfPack\Contract\Transfer\Request\RequestInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function is_file;
use function is_readable;
use function sprintf;

class BaseRequest implements RequestInterface
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
     * @throws InvalidArgumentException when $path is empty
     * @throws InvalidArgumentException when $path is not a readable file
     */
    private function validatePath(?string $path): string
    {
        if ('' === $path = trim((string) $path)) {
            throw new InvalidArgumentException('The path cannot be empty.');
        }

        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('The file "%s" is not readable.', $path));
        }

        return $path;
    }
}
