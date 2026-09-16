<?php

namespace OneToMany\PdfPack\Tests;

use OneToMany\PdfPack\Bridge\Mock\MockProvider;
use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\Resource\Registry;
use OneToMany\PdfPack\Tests\Fixture\Bridge\ImagickProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
final class PdfClientTest extends TestCase
{
    public function testUsingProviderMemoizesFilesResources(): void
    {
        $registry = new Registry([
            new MockProvider(),
            new PopplerProvider(),
            new ImagickProvider(),
        ]);

        $pdfClient = new PdfClient('poppler', $registry);

        $pdfClient->use('mock');
        $mockFiles = $pdfClient->files;

        $pdfClient->use('poppler');
        $popplerFiles = $pdfClient->files;

        $this->assertNotSame($mockFiles, $popplerFiles);
        $this->assertSame($mockFiles, $pdfClient->use(' MOCK ')->files);
        $this->assertSame($popplerFiles, $pdfClient->use(' POPPLER ')->files);
        $this->assertSame(1, $pdfClient->use('imagick')->files->read(__FILE__)->pageCount);
    }

    public function testProviderCannotBeEmpty(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The provider cannot be empty.');

        new PdfClient('', new Registry([new PopplerProvider()]));
    }
}
