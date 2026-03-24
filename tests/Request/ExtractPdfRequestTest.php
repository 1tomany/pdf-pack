<?php

namespace OneToMany\PdfPack\Tests\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\ExtractPdfRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function random_int;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class ExtractPdfRequestTest extends TestCase
{
    public function testConstructorRequiresNonEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The path cannot be empty.');

        new ExtractPdfRequest('');
    }

    public function testConstructorRequiresReadableFile(): void
    {
        $path = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($path);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file "'.$path.'" is not readable.');

        new ExtractPdfRequest($path);
    }

    public function testConstructorRequiresPositiveNonZeroFirstPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The first page must be a positive integer.');

        new ExtractPdfRequest(__DIR__.'/../.data/label.pdf', firstPage: 0); // @phpstan-ignore argument.type
    }

    public function testConstructorRequiresPositiveNonZeroLastPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The last page must be a positive integer.');

        new ExtractPdfRequest(__DIR__.'/../.data/label.pdf', lastPage: 0); // @phpstan-ignore argument.type
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMinimumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be 48 DPI or larger.');

        new ExtractPdfRequest(__DIR__.'/../.data/label.pdf', resolution: random_int(0, 32)); // @phpstan-ignore argument.type
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMaximumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be 300 DPI or smaller.');

        new ExtractPdfRequest(__DIR__.'/../.data/label.pdf', resolution: random_int(301, 1000)); // @phpstan-ignore argument.type
    }

    #[DataProvider('providerConstructorArguments')]
    public function testConstructor(
        string $path,
        int $firstPage,
        int $lastPage,
        OutputType $outputType,
        int $resolution,
    ): void {
        $request = new ExtractPdfRequest($path, $firstPage, $lastPage, $outputType, $resolution); // @phpstan-ignore-line

        $this->assertEquals($path, $request->getPath());
        $this->assertEquals($firstPage, $request->getFirstPage());
        $this->assertEquals($lastPage, $request->getLastPage());
        $this->assertEquals($outputType, $request->getOutputType());
        $this->assertEquals($resolution, $request->getResolution());
    }

    /**
     * @return list<list<int|string|OutputType>>
     */
    public static function providerConstructorArguments(): array
    {
        $path = __DIR__.'/../.data/label.pdf';

        $resolution = random_int(
            ExtractPdfRequest::MIN_RESOLUTION,
            ExtractPdfRequest::MAX_RESOLUTION,
        );

        $provider = [
            [$path, 1, 1, OutputType::Png, $resolution],
            [$path, 2, 4, OutputType::Jpeg, $resolution],
            [$path, 2, 4, OutputType::Text, $resolution],
        ];

        return $provider;
    }

    public function testSettingFirstPageGreaterThanLastPageClampsLastPageToFirstPageWhenLastPageIsNotNull(): void
    {
        $request = new ExtractPdfRequest(__DIR__.'/../.data/label.pdf');

        $this->assertSame(1, $request->getFirstPage());
        $this->assertSame(null, $request->getLastPage());

        $request->toPage(random_int(2, 10));
        $this->assertGreaterThan($request->getFirstPage(), $request->getLastPage());

        $request->fromPage($request->getLastPage() + random_int(2, 10));
        $this->assertEquals($request->getFirstPage(), $request->getLastPage());
    }

    public function testSettingLastPageLessThanFirstPageClampsFirstPageToLastPage(): void
    {
        $page = random_int(2, 10);

        $request = new ExtractPdfRequest(__DIR__.'/../.data/label.pdf', $page, $page);
        $this->assertEquals($request->getLastPage(), $request->getFirstPage());

        $request->toPage($request->getLastPage() - 1);
        $this->assertEquals($request->getLastPage(), $request->getFirstPage());
    }
}
