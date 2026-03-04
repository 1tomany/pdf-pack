<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Request\Trait\ValidatePathTrait;

class ReadRequest
{
    use ValidatePathTrait;

    private string $path;

    public function __construct(string $path)
    {
        $this->atPath($path);
    }

    public function atPath(string $path): static
    {
        $this->path = $this->validatePath($path);

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
