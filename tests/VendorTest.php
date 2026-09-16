<?php

namespace OneToMany\PdfPack\Tests;

use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Vendor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
final class VendorTest extends TestCase
{
    public function testCreatingVendorNormalizesString(): void
    {
        $this->assertSame(Vendor::Poppler, Vendor::create(' POPPLER '));
        $this->assertSame(Vendor::Mock, Vendor::create(Vendor::Mock));
    }

    public function testCreatingVendorRequiresValidValue(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The vendor "invalid" is not valid.');

        Vendor::create('invalid');
    }
}
