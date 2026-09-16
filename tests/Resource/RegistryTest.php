<?php

namespace OneToMany\PdfPack\Tests\Resource;

use OneToMany\PdfPack\Bridge\Mock\MockProvider;
use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Resource\Registry;
use OneToMany\PdfPack\Vendor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ResourceTests')]
final class RegistryTest extends TestCase
{
    public function testGettingProvider(): void
    {
        $provider = new PopplerProvider();

        $this->assertSame($provider, new Registry([$provider])->get(Vendor::Poppler));
    }

    public function testGettingProviderRequiresRegistration(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The "poppler" provider is not registered.');

        new Registry([])->get(Vendor::Poppler);
    }

    public function testRegisteringDuplicateProviderFails(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The "mock" provider is already registered.');

        new Registry([new MockProvider(), new MockProvider()]);
    }
}
