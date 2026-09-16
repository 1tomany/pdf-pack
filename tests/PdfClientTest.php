<?php

namespace OneToMany\PdfPack\Tests;

use OneToMany\PdfPack\Bridge\Mock\MockProvider;
use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\Resource\Registry;
use OneToMany\PdfPack\Vendor;
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
        ]);

        $pdfClient = new PdfClient(Vendor::Poppler, $registry);

        $pdfClient->use(Vendor::Mock);
        $mockFiles = $pdfClient->files;

        $pdfClient->use(Vendor::Poppler);
        $popplerFiles = $pdfClient->files;

        $this->assertNotSame($mockFiles, $popplerFiles);
        $this->assertSame($mockFiles, $pdfClient->use(Vendor::Mock)->files);
        $this->assertSame($popplerFiles, $pdfClient->use(Vendor::Poppler)->files);
    }
}
