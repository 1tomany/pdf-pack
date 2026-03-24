<?php

namespace OneToMany\PdfPack\Response;

use OneToMany\PdfPack\Contract\Enum\OutputType;

use function base64_encode;
use function hash;
use function max;
use function sprintf;
use function strlen;

final class ConvertPdfResponse implements \Stringable
{
    /**
     * @var ?non-empty-lowercase-string
     */
    private ?string $hash = null;

    /**
     * @var ?non-negative-int
     */
    private ?int $size = null;

    /**
     * @param positive-int $page
     */
    public function __construct(
        private readonly OutputType $type,
        private readonly string $data,
        private readonly int $page = 1,
    ) {
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
        if (null === $this->hash) {
            $this->hash = hash('sha256', $this->getData());
        }

        return $this->hash;
    }

    /**
     * @return non-negative-int
     */
    public function getSize(): int
    {
        if (null === $this->size) {
            $this->size = strlen($this->getData());
        }

        return $this->size;
    }

    /**
     * @return positive-int
     */
    public function getPage(): int
    {
        return max(1, $this->page);
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
