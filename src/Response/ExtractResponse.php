<?php

namespace OneToMany\PdfPack\Response;

use OneToMany\PdfPack\Contract\Enum\OutputType;

use function base64_encode;
use function max;
use function sprintf;
use function trim;

final readonly class ExtractResponse implements \Stringable
{
    /**
     * @param positive-int $page
     */
    public function __construct(
        private OutputType $type,
        private string $data,
        private int $page = 1,
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
