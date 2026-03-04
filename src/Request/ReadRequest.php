<?php

namespace OneToMany\PdfPack\Request;

use function trim;

class ReadRequest
{
    /** @var ?non-empty-string */
    private ?string $path = null;

    public function __construct(?string $path)
    {
        $this->atPath($path);
    }

    public function atPath(?string $path): static
    {
        $this->path = trim($path ?? '') ?: null;

        return $this;
    }

    /**
     * @return ?non-empty-string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
}
