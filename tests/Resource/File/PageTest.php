<?php

namespace OneToMany\PdfPack\Tests\Resource\File;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Resource\File\Page;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function hash;
use function strlen;

#[Group('UnitTests')]
#[Group('ResourceTests')]
final class PageTest extends TestCase
{
    public function testConstructingPageCalculatesMetadata(): void
    {
        $text = 'PDF Page';

        $size = strlen($text);
        $hash = hash('sha256', $text);

        $page = new Page(OutputType::Text, $text, 3);

        $this->assertSame(3, $page->page);
        $this->assertSame($size, $page->size);
        $this->assertSame($hash, $page->hash);
        $this->assertSame('page-3.txt', $page->getName());
    }

    public function testPageCannotBeNegative(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The page must be greater than 0.');

        new Page(OutputType::Text, '', -1);
    }

    #[DataProvider('providerOutputTypeAndFormat')]
    public function testConvertingToDataUri(
        OutputType $outputType,
        string $format,
    ): void {
        $this->assertSame('data:'.$format.';base64,'.base64_encode('data'), new Page($outputType, 'data')->toDataUri());
    }

    /**
     * @return list<array{OutputType, non-empty-lowercase-string}>
     */
    public static function providerOutputTypeAndFormat(): array
    {
        return [
            [OutputType::Jpeg, 'image/jpeg'],
            [OutputType::Png, 'image/png'],
            [OutputType::Text, 'text/plain'],
        ];
    }
}
