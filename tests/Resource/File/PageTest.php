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

#[Group('UnitTests')]
#[Group('ResourceTests')]
final class PageTest extends TestCase
{
    public function testConstructingPageCalculatesMetadata(): void
    {
        $page = new Page(OutputType::Text, 'PDF Page', 3);

        $this->assertSame(hash('sha256', 'PDF Page'), $page->hash);
        $this->assertSame(8, $page->size);
        $this->assertSame(3, $page->page);
        $this->assertSame('page-3.txt', $page->getName());
    }

    public function testPageCannotBeNegative(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The page cannot be negative.');

        new Page(OutputType::Text, '', -1);
    }

    #[DataProvider('providerOutputTypeAndDataUri')]
    public function testConvertingToDataUri(OutputType $outputType, string $mimeType): void
    {
        $page = new Page($outputType, 'data');

        $this->assertSame('data:'.$mimeType.';base64,'.base64_encode('data'), $page->toDataUri());
    }

    /**
     * @return list<array{OutputType, string}>
     */
    public static function providerOutputTypeAndDataUri(): array
    {
        return [
            [OutputType::Jpeg, 'image/jpeg'],
            [OutputType::Png, 'image/png'],
            [OutputType::Text, 'text/plain'],
        ];
    }
}
