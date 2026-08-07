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
    private OutputType $type;
    private string $data;

    /**
     * @var non-empty-lowercase-string
     */
    private string $hash;

    /**
     * @var positive-int
     */
    private int $page;

    /**
     * @var non-negative-int
     */
    private int $size;

    /**
     * @throws InvalidArgumentException when $page is not greater than 0
     */
    public function __construct(
        OutputType $type,
        string $data,
        int $page = 1,
    ) {
        $this->type = $type;
        $this->data = $data;

        $this->hash = hash('sha256', $data);

        if ($page < 1) {
            throw new InvalidArgumentException('The page must be greater than 0.');
        }

        $this->page = $page;
        $this->size = strlen($data);
    }

    public static function asJpeg(
        string $data,
        int $page = 1,
    ): static {
        return new static(OutputType::Jpeg, $data, $page);
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public function getType(): OutputType
    {
        return $this->type;
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
     * @return positive-int
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
        return sprintf('page-%d.%s', $this->getPage(), $this->getType()->getExtension());
    }

    /**
     * @return non-empty-string
     */
    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->type->getFormat(), base64_encode($this->getData()));
    }
}
