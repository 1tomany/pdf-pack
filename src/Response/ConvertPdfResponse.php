<?php

namespace OneToMany\PdfPack\Response;

use OneToMany\PdfPack\Contract\Enum\OutputType;

use function base64_encode;
use function hash;
use function max;
use function sprintf;

final readonly class ConvertPdfResponse implements \Stringable
{
    /**
     * @var non-empty-lowercase-string
     */
    private string $hash;

    /**
     * @param positive-int $page
     */
    public function __construct(
        private OutputType $type,
        private string $data,
        private int $page = 1,
    ) {
        $this->hash = hash('sha256', $data);
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
     * @return non-negative-int
     */
    public function getSize(): int
    {
        return \strlen($this->getData());
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
