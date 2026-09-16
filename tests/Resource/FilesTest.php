<?php

namespace OneToMany\PdfPack\Tests\Resource;

use OneToMany\PdfPack\Bridge\Mock\MockProvider;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Exception\RangeException;
use OneToMany\PdfPack\Resource\Files;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ResourceTests')]
final class FilesTest extends TestCase
{
    /**
     * @param class-string<DomainException|RangeException> $exceptionType
     */
    #[DataProvider('providerInvalidConversionArguments')]
    public function testConvertingValidatesArguments(
        int $fromPage,
        ?int $toPage,
        int $resolution,
        string $exceptionType,
        string $message,
    ): void {
        $this->expectException($exceptionType);
        $this->expectExceptionMessageIs($message);

        new Files(new MockProvider())->convert(__FILE__, $fromPage, $toPage, resolution: $resolution); // @phpstan-ignore-line
    }

    /**
     * @return list<array{int, ?int, int, string}>
     */
    public static function providerInvalidConversionArguments(): array
    {
        return [
            [0, null, 72, DomainException::class, 'The first page must be greater than 0.'],
            [1, 0, 72, DomainException::class, 'The last page must be greater than 0.'],
            [2, 1, 72, DomainException::class, 'The last page must be greater than or equal to the first page.'],
            [1, null, 47, RangeException::class, 'The resolution must be 48 DPI or larger.'],
            [1, null, 301, RangeException::class, 'The resolution must be 300 DPI or smaller.'],
        ];
    }

    public function testReadingRequiresReadablePath(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The path cannot be empty.');

        new Files(new MockProvider())->read('');
    }
}
