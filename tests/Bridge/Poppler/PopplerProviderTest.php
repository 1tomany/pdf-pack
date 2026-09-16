<?php

namespace OneToMany\PdfPack\Tests\Bridge\Poppler;

use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Exception\RuntimeException;
use OneToMany\PdfPack\Resource\File\Page;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function iterator_to_array;

#[Large]
#[Group('UnitTests')]
#[Group('ProviderTests')]
#[Group('PopplerTests')]
final class PopplerProviderTest extends TestCase
{
    #[DataProvider('providerInvalidBinaryAndOperation')]
    public function testOperationsRequireValidBinaries(string $binary, ?OutputType $outputType): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The binary "'.$binary.'" could not be found.');

        $provider = match ($outputType) {
            null => new PopplerProvider(pdfInfoBinary: $binary),
            OutputType::Text => new PopplerProvider(pdfToTextBinary: $binary),
            default => new PopplerProvider(pdfToPpmBinary: $binary),
        };

        if (null === $outputType) {
            $provider->read(__FILE__);

            return;
        }

        $provider->convert(__DIR__.'/../../../data/files/pages-1.pdf', toPage: 1, outputType: $outputType)->current();
    }

    /**
     * @return list<array{string, ?OutputType}>
     */
    public static function providerInvalidBinaryAndOperation(): array
    {
        return [
            ['invalid_pdfinfo_binary', null],
            ['invalid_pdftoppm_binary', OutputType::Jpeg],
            ['invalid_pdftotext_binary', OutputType::Text],
        ];
    }

    public function testReadingRequiresValidPdf(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIsOrContains('May not be a PDF file');

        new PopplerProvider()->read(__FILE__);
    }

    #[DataProvider('providerPathAndPageCount')]
    public function testReadingFile(string $path, int $pageCount): void
    {
        $this->assertSame($pageCount, new PopplerProvider()->read($path)->pageCount);
    }

    /**
     * @return list<array{string, int}>
     */
    public static function providerPathAndPageCount(): array
    {
        return [
            [__DIR__.'/../../../data/files/pages-1.pdf', 1],
            [__DIR__.'/../../../data/files/pages-2.pdf', 2],
            [__DIR__.'/../../../data/files/pages-3.pdf', 3],
            [__DIR__.'/../../../data/files/pages-4.pdf', 4],
        ];
    }

    public function testConvertingRequiresValidPdf(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIsOrContains('May not be a PDF file');

        new PopplerProvider()->convert(__FILE__, toPage: 1)->current();
    }

    public function testConvertingRequiresValidPage(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIsOrContains('Wrong page range given');

        new PopplerProvider()->convert(__DIR__.'/../../../data/files/pages-1.pdf', 2, 2)->current();
    }

    #[DataProvider('providerPathRangeAndCount')]
    public function testConvertingPageRanges(string $path, int $fromPage, ?int $toPage, int $count): void
    {
        $this->assertCount($count, iterator_to_array(new PopplerProvider()->convert($path, $fromPage, $toPage)));
    }

    /**
     * @return list<array{string, int, ?int, int}>
     */
    public static function providerPathRangeAndCount(): array
    {
        return [
            [__DIR__.'/../../../data/files/pages-1.pdf', 1, null, 1],
            [__DIR__.'/../../../data/files/pages-2.pdf', 1, 2, 2],
            [__DIR__.'/../../../data/files/pages-3.pdf', 2, null, 2],
            [__DIR__.'/../../../data/files/pages-4.pdf', 2, 4, 3],
            [__DIR__.'/../../../data/files/pages-4.pdf', 4, null, 1],
        ];
    }

    #[DataProvider('providerPathPageAndText')]
    public function testConvertingPdfToText(string $path, int $pageNumber, string $text): void
    {
        $pages = iterator_to_array(new PopplerProvider()->convert($path, $pageNumber, $pageNumber, OutputType::Text));

        $this->assertCount(1, $pages);
        $this->assertSame($pageNumber, $pages[0]->page);
        $this->assertStringContainsString($text, $pages[0]->data);
    }

    /**
     * @return list<array{string, int, string}>
     */
    public static function providerPathPageAndText(): array
    {
        return [
            [__DIR__.'/../../../data/files/pages-2.pdf', 1, 'Amazon Simple Storage Service'],
            [__DIR__.'/../../../data/files/pages-2.pdf', 2, 'Storage Service: API Reference'],
            [__DIR__.'/../../../data/files/pages-3.pdf', 3, 'Learn more about the AWS CLI'],
            [__DIR__.'/../../../data/files/pages-4.pdf', 4, 'API Version 2006-03-01 iv'],
        ];
    }

    #[DataProvider('providerImageArgumentsAndHash')]
    public function testConvertingPdfToImage(
        string $path,
        int $pageNumber,
        OutputType $outputType,
        int $resolution,
        string $hash,
    ): void {
        $page = new PopplerProvider()->convert($path, $pageNumber, $pageNumber, $outputType, $resolution)->current();

        $this->assertInstanceOf(Page::class, $page);
        $this->assertSame($hash, $page->hash);

        $image = imagecreatefromstring($page->data);
        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertGreaterThan(0, imagesx($image));
        $this->assertGreaterThan(0, imagesy($image));
    }

    /**
     * @return list<array{string, int, OutputType, int, string}>
     */
    public static function providerImageArgumentsAndHash(): array
    {
        return [
            [__DIR__.'/../../../data/files/pages-1.pdf', 1, OutputType::Jpeg, 48, '28933b0d9af70074616a15d721f34c3f5d3bb6bb80f07a6992b572d8c3e80697'],
            [__DIR__.'/../../../data/files/pages-1.pdf', 1, OutputType::Jpeg, 150, '1cb6e6f67bcffc8e16861c4789b67e06d4a59465933b98317183cff624a76df9'],
            [__DIR__.'/../../../data/files/pages-1.pdf', 1, OutputType::Png, 300, 'beba09056cf8eb7f1359bc8fc1b486c35e0bffa40b216524fe61ffd176658c0b'],
            [__DIR__.'/../../../data/files/pages-4.pdf', 4, OutputType::Jpeg, 72, 'f7553db6b12ad98c4d790788bc11823cd2a27f4bae35b227566b7927d177330f'],
        ];
    }
}
