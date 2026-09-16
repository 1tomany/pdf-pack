<?php

namespace OneToMany\PdfPack;

use OneToMany\PdfPack\Exception\DomainException;

use function sprintf;
use function strtolower;
use function trim;

enum Vendor: string
{
    case Mock = 'mock';
    case Poppler = 'poppler';

    /**
     * @throws DomainException when the vendor is not valid
     */
    public static function create(string|self $vendor): self
    {
        if ($vendor instanceof self) {
            return $vendor;
        }

        $vendor = strtolower(trim($vendor));

        try {
            return self::from($vendor);
        } catch (\ValueError $e) {
            throw new DomainException(sprintf('The vendor "%s" is not valid.', $vendor), previous: $e);
        }
    }

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
