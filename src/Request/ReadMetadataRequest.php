<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Request\ReadMetadataRequestInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function is_file;
use function is_readable;
use function sprintf;
use function trim;

class ReadMetadataRequest implements ReadMetadataRequestInterface
{
    /**
     * @var non-empty-string
     */
    protected string $filePath;

    public function __construct(?string $filePath)
    {
        $this->setFilePath($filePath);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $filePath = trim($filePath ?? '') ?: null;

        if (empty($filePath)) {
            throw new InvalidArgumentException('The file path cannot be empty.');
        }

        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('The file path "%s" does not exist or is not readable.', $filePath));
        }

        $this->filePath = $filePath;

        return $this;
    }
}
