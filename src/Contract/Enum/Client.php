<?php

namespace OneToMany\PdfPack\Contract\Enum;

enum Client: string
{
    case Mock = 'mock';
    case Poppler = 'poppler';

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
