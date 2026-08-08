<?php

namespace OneToMany\PdfPack\Transfer\Record;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Transfer\Record\RecordInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;

use function base64_encode;
use function hash;
use function sprintf;
use function strlen;

final readonly class PageRecord implements \Stringable, RecordInterface
{
    private OutputType $outputType;
    private string $data;

    /**
     * @var non-empty-lowercase-string
     */
    private string $hash;

    /**
     * @var non-negative-int
     */
    private int $page;

    /**
     * @var non-negative-int
     */
    private int $size;

    /**
     * @throws InvalidArgumentException when $page is negative
     */
    public function __construct(
        OutputType $outputType,
        string $data,
        int $page = 1,
    ) {
        $this->outputType = $outputType;

        $this->data = $data;
        $this->hash = hash('sha256', $data);

        if ($page < 0) {
            throw new InvalidArgumentException('The page cannot be negative.');
        }

        $this->page = $page;
        $this->size = strlen($data);
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public static function asJpeg(
        string $data,
        int $page = 1,
    ): static {
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
        return sprintf('page-%d.%s', $this->getPage(), $this->getOutputType()->getExtension());
    }

    /**
     * @return non-empty-string
     */
    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->outputType->getFormat(), base64_encode($this->getData()));
    }
}
