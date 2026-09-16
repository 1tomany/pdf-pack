<?php

namespace OneToMany\PdfPack\Tests;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;
use OneToMany\PdfPack\Resource\Registry;
use OneToMany\PdfPack\Vendor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
final class PdfClientTest extends TestCase
{
    public function testUsingProviderMutatesCurrentFilesResource(): void
    {
        $client = $this->createPdfClient(new PopplerTestProvider(), new MockTestProvider());

        $this->assertSame(1, $client->files->read(__FILE__)->pageCount);
        $this->assertSame($client, $client->use(' MOCK '));
        $this->assertSame(2, $client->files->read(__FILE__)->pageCount);

        $client->use(Vendor::Poppler);

        $this->assertSame(1, $client->files->read(__FILE__)->pageCount);
    }

    public function testUsingProviderMemoizesFilesResources(): void
    {
        $client = $this->createPdfClient(new PopplerTestProvider(), new MockTestProvider());
        $popplerFiles = $client->files;

        $client->use(Vendor::Mock);
        $mockFiles = $client->files;

        $this->assertNotSame($popplerFiles, $mockFiles);
        $this->assertSame($popplerFiles, $client->use(Vendor::Poppler)->files);
        $this->assertSame($mockFiles, $client->use(Vendor::Mock)->files);
    }

    public function testPropertyMutabilityMatchesFacadeState(): void
    {
        $files = new \ReflectionProperty(PdfClient::class, 'files');
        $providers = new \ReflectionProperty(PdfClient::class, 'providers');
        $filesByVendor = new \ReflectionProperty(PdfClient::class, 'filesByVendor');

        $this->assertTrue($files->isPublic());
        $this->assertTrue($files->isPrivateSet());
        $this->assertFalse($files->isReadOnly());
        $this->assertTrue($providers->isReadOnly());
        $this->assertFalse($filesByVendor->isReadOnly());
    }

    public function testDirectMethodsAreAliasesForFilesResource(): void
    {
        $client = $this->createPdfClient(new PopplerTestProvider());

        $this->assertEquals($client->files->read(__FILE__), $client->read(__FILE__));
        $this->assertEquals($client->files->convert(__FILE__)->current(), $client->convert(__FILE__)->current());
    }

    private function createPdfClient(ProviderInterface ...$providers): PdfClient
    {
        return new PdfClient(Vendor::Poppler, new Registry($providers));
    }
}

class PopplerTestProvider implements ProviderInterface
{
    #[\Override]
    public static function getVendor(): Vendor
    {
        return Vendor::Poppler;
    }

    #[\Override]
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = 72,
    ): \Generator {
        yield new Page($outputType, 'page', $fromPage);
    }

    #[\Override]
    public function read(string $path): File
    {
        return new File($path, 1);
    }
}

final class MockTestProvider extends PopplerTestProvider
{
    #[\Override]
    public static function getVendor(): Vendor
    {
        return Vendor::Mock;
    }

    #[\Override]
    public function read(string $path): File
    {
        return new File($path, 2);
    }
}
