<?php

namespace OneToMany\PdfPack\Tests\Resource;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;
use OneToMany\PdfPack\Resource\Files;
use OneToMany\PdfPack\Vendor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ResourceTests')]
final class FilesTest extends TestCase
{
    public function testReadingRequiresReadablePath(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The path cannot be empty.');

        new Files(new RecordingProvider())->read('');
    }

    #[DataProvider('providerInvalidConversionArguments')]
    public function testConvertingValidatesArguments(
        int $fromPage,
        ?int $toPage,
        int $resolution,
        string $message,
    ): void {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs($message);

        new Files(new RecordingProvider())->convert(__FILE__, $fromPage, $toPage, resolution: $resolution);
    }

    /**
     * @return list<array{int, ?int, int, string}>
     */
    public static function providerInvalidConversionArguments(): array
    {
        return [
            [0, null, 72, 'The page must be greater than 0.'],
            [1, 0, 72, 'The page must be greater than 0.'],
            [2, 1, 72, 'The ending page must be greater than or equal to the starting page.'],
            [1, null, 47, 'The resolution must be 48 DPI or larger.'],
            [1, null, 301, 'The resolution must be 300 DPI or smaller.'],
        ];
    }

    public function testConvertingDelegatesArgumentsLazily(): void
    {
        $provider = new RecordingProvider();
        $pages = new Files($provider)->convert(__FILE__, 2, 3, OutputType::Png, 150);

        $this->assertSame(0, $provider->convertCalls);
        $this->assertInstanceOf(Page::class, $pages->current());
        $this->assertSame(1, $provider->convertCalls);
        $this->assertSame([__FILE__, 2, 3, OutputType::Png, 150], $provider->arguments);
    }
}

final class RecordingProvider implements ProviderInterface
{
    public int $convertCalls = 0;

    /**
     * @var array{string, int, ?int, OutputType, int}|null
     */
    public ?array $arguments = null;

    #[\Override]
    public static function getVendor(): Vendor
    {
        return Vendor::Mock;
    }

    #[\Override]
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = 72,
    ): \Generator {
        ++$this->convertCalls;
        $this->arguments = [$path, $fromPage, $toPage, $outputType, $resolution];

        yield new Page($outputType, '', $fromPage);
    }

    #[\Override]
    public function read(string $path): File
    {
        return new File($path, 1);
    }
}
