<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function sprintf;

class ConvertPdfRequest extends BaseRequest
{
    /**
     * The default DPI for rasterized pages.
     *
     * @var positive-int
     */
    public const int DEFAULT_RESOLUTION = 72;

    /**
     * The minimum DPI pages can be rasterized as.
     *
     * @var positive-int
     */
    public const int MIN_RESOLUTION = 48;

    /**
     * The maximum DPI pages can be rasterized as.
     *
     * @var positive-int
     */
    public const int MAX_RESOLUTION = 300;

    /**
     * @param positive-int $firstPage
     * @param ?positive-int $lastPage
     * @param int<self::MIN_RESOLUTION, self::MAX_RESOLUTION> $resolution
     */
    public function __construct(
        ?string $path,
        private int $firstPage = 1,
        private ?int $lastPage = null,
        private OutputType $outputType = OutputType::Jpeg,
        private int $resolution = self::DEFAULT_RESOLUTION,
    ) {
        parent::__construct($path);

        $this->fromPage($firstPage);
        $this->toPage($lastPage);
        $this->asOutputType($outputType);
        $this->atResolution($resolution);
    }

    /**
     * @throws InvalidArgumentException when the first page is not a positive integer
     */
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

    /**
     * @throws InvalidArgumentException when the last page is not a positive integer
     */
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

    /**
     * @throws InvalidArgumentException when the resolution is less than self::MIN_RESOLUTION
     * @throws InvalidArgumentException when the resolution is greater than self::MAX_RESOLUTION
     */
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
