<?php

namespace OneToMany\PdfPack\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Request\ExtractDataRequestInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function sprintf;

class ExtractDataRequest extends ReadMetadataRequest implements ExtractDataRequestInterface
{
    /**
     * @var positive-int
     */
    protected int $firstPage = 1;
    protected ?int $lastPage = null;
    protected OutputType $outputType = OutputType::Jpg;

    /**
     * @var int<self::MIN_RESOLUTION, self::MAX_RESOLUTION>
     */
    protected int $resolution = self::DEFAULT_RESOLUTION;

    public function __construct(
        ?string $filePath,
        int $firstPage = 1,
        ?int $lastPage = 1,
        OutputType $outputType = OutputType::Jpg,
        int $resolution = self::DEFAULT_RESOLUTION,
    ) {
        $this->setFilePath($filePath);
        $this->setFirstPage($firstPage);
        $this->setLastPage($lastPage);
        $this->setOutputType($outputType);
        $this->setResolution($resolution);
    }

    public function getFirstPage(): int
    {
        return $this->firstPage;
    }

    public function setFirstPage(int $firstPage): static
    {
        if ($firstPage < 1) {
            throw new InvalidArgumentException('The first page number must be a positive non-zero integer.');
        }

        $this->firstPage = $firstPage;

        if ($firstPage > $this->getLastPage()) {
            $this->setLastPage($firstPage);
        }

        return $this;
    }

    public function getLastPage(): ?int
    {
        return $this->lastPage;
    }

    public function setLastPage(?int $lastPage): static
    {
        if (null !== $lastPage) {
            if ($lastPage < 1) {
                throw new InvalidArgumentException('The last page number must be a positive non-zero integer.');
            }

            if ($lastPage < $this->getFirstPage()) {
                $this->setFirstPage($lastPage);
            }
        }

        $this->lastPage = $lastPage;

        return $this;
    }

    public function getOutputType(): OutputType
    {
        return $this->outputType;
    }

    public function setOutputType(OutputType $outputType): static
    {
        $this->outputType = $outputType;

        return $this;
    }

    public function getResolution(): int
    {
        return $this->resolution;
    }

    public function setResolution(int $resolution): static
    {
        if ($resolution < self::MIN_RESOLUTION || $resolution > self::MAX_RESOLUTION) {
            throw new InvalidArgumentException(sprintf('The resolution must be an integer between %d and %d.', self::MIN_RESOLUTION, self::MAX_RESOLUTION));
        }

        $this->resolution = $resolution;

        return $this;
    }
}
