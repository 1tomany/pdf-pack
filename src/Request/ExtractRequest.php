<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\Trait\ValidatePathTrait;

use function sprintf;

class ExtractRequest
{
    use ValidatePathTrait;

    private string $path;

    /** @var positive-int */
    private int $firstPage = 1;

    /** @var ?positive-int */
    private ?int $lastPage = null;

    private OutputType $outputType = OutputType::Jpeg;

    /** @var int<48, 300> */
    private int $resolution = self::DEFAULT_RESOLUTION;

    public const int DEFAULT_RESOLUTION = 72;
    public const int MIN_RESOLUTION = 48;
    public const int MAX_RESOLUTION = 300;

    public function __construct(
        string $path,
        int $firstPage = 1,
        ?int $lastPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = self::DEFAULT_RESOLUTION,
    ) {
        $this->atPath($path);
        $this->fromPage($firstPage);
        $this->toPage($lastPage);
        $this->asOutputType($outputType);
        $this->atResolution($resolution);
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

    public function fromPage(int $page): static
    {
        if ($page < 1) {
            throw new InvalidArgumentException('The first page must be a positive integer.');
        }

        $this->firstPage = $page;

        if (null !== $this->lastPage && $page > $this->lastPage) {
            $this->toPage($page);
        }

        return $this;
    }

    /**
     * @return positive-int
     */
    public function getFirstPage(): int
    {
        return $this->firstPage;
    }

    public function toPage(?int $page): static
    {
        if (null !== $page) {
            if ($page < 1) {
                throw new InvalidArgumentException('The last page must be a positive integer.');
            }

            if ($page < $this->firstPage) {
                $this->fromPage($page);
            }
        }

        $this->lastPage = $page;

        return $this;
    }

    /**
     * @return ?positive-int
     */
    public function getLastPage(): ?int
    {
        return $this->lastPage;
    }

    public function asJpegOutput(): static
    {
        return $this->asOutputType(OutputType::Jpeg);
    }

    public function asPngOutput(): static
    {
        return $this->asOutputType(OutputType::Png);
    }

    public function asTextOutput(): static
    {
        return $this->asOutputType(OutputType::Text);
    }

    public function asOutputType(OutputType $outputType): static
    {
        $this->outputType = $outputType;

        return $this;
    }

    public function getOutputType(): OutputType
    {
        return $this->outputType;
    }

    public function atResolution(int $resolution): static
    {
        if ($resolution < self::MIN_RESOLUTION) {
            throw new InvalidArgumentException(sprintf('The resolution must be %d DPI or larger.', self::MIN_RESOLUTION));
        }

        if ($resolution > self::MAX_RESOLUTION) {
            throw new InvalidArgumentException(sprintf('The resolution must be %d DPI or smaller.', self::MAX_RESOLUTION));
        }

        $this->resolution = $resolution;

        return $this;
    }

    /**
     * @return int<self::MIN_RESOLUTION, self::MAX_RESOLUTION>
     */
    public function getResolution(): int
    {
        return $this->resolution;
    }
}
