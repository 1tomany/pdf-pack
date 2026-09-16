<?php

namespace OneToMany\PdfPack\Resource\File;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\DomainException;

use function base64_encode;
use function hash;
use function sprintf;
use function strlen;

final readonly class Page implements \Stringable
{
    /**
     * @var positive-int
     */
    public int $page;

    /**
     * @var non-negative-int
     */
    public int $size;

    /**
     * @var non-empty-lowercase-string
     */
    public string $hash;

    /**
     * @throws DomainException when the page is less than 0
     */
    public function __construct(
        public OutputType $outputType,
        public string $data,
        int $page = 1,
    ) {
        if ($page <= 0) {
            throw new DomainException('The page must be greater than 0.');
        }

        $this->page = $page;

        $this->size = strlen($data);
        $this->hash = hash('sha256', $data);
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public static function asJpeg(string $data, int $page = 1): static
    {
        return new static(OutputType::Jpeg, $data, $page);
    }

    public function getOutputType(): OutputType
    {
        return $this->outputType;
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return non-negative-int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return non-negative-int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return sprintf('page-%d.%s', $this->page, $this->outputType->getExtension());
    }

    /**
     * @return non-empty-string
     */
    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->outputType->getFormat(), base64_encode($this->data));
    }
}
