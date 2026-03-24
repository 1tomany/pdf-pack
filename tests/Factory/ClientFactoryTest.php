<?php

namespace OneToMany\PdfPack\Tests\Factory;

use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Factory\ClientFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function uniqid;

#[Group('UnitTests')]
#[Group('FactoryTests')]
final class ClientFactoryTest extends TestCase
{
    public function testCreatingClientRequiresRegisteredClient(): void
    {
        $vendor = uniqid('vendor_');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The vendor "'.$vendor.'" does not have a registered client.');

        new ClientFactory()->create($vendor);
    }
}
