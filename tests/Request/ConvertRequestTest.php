<?php

namespace OneToMany\PdfPack\Tests\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function random_int;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class ConvertRequestTest extends TestCase
{
    public function testConstructorRequiresNonEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The path cannot be empty.');

        new ConvertRequest('');
    }

    public function testConstructorRequiresReadableFile(): void
    {
        $path = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($path);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The file "'.$path.'" is not readable.');

        new ConvertRequest($path);
    }

    public function testConstructorRequiresPositiveNonZeroFirstPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The page must be greater than 0.');

        new ConvertRequest(__DIR__.'/../../config/files/label.pdf', firstPage: 0);
    }

    public function testConstructorRequiresPositiveNonZeroLastPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The page must be greater than 0.');

        new ConvertRequest(__DIR__.'/../../config/files/label.pdf', lastPage: 0);
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMinimumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The resolution must be 48 DPI or larger.');

        new ConvertRequest(__DIR__.'/../../config/files/label.pdf', resolution: random_int(0, 32));
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMaximumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The resolution must be 300 DPI or smaller.');

        new ConvertRequest(__DIR__.'/../../config/files/label.pdf', resolution: random_int(301, 1000));
    }

    #[DataProvider('providerConstructorArguments')]
    public function testConstructor(
        string $path,
        int $firstPage,
        int $lastPage,
        OutputType $outputType,
        int $resolution,
    ): void {
        $request = new ConvertRequest($path, $firstPage, $lastPage, $outputType, $resolution);

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
        $path = __DIR__.'/../../config/files/label.pdf';

        $resolution = random_int(
            ConvertRequest::MIN_RESOLUTION,
            ConvertRequest::MAX_RESOLUTION,
        );

        $provider = [
            [$path, 1, 1, OutputType::Png, $resolution],
            [$path, 2, 4, OutputType::Jpeg, $resolution],
            [$path, 2, 4, OutputType::Text, $resolution],
        ];

        return $provider;
    }

    public function testToImageRequiresOutputTypeToBeImage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The output type must be an image.');

        ConvertRequest::toImage(__DIR__.'/../../config/files/label.pdf', 1, 1, OutputType::Text);
    }

    public function testToTextSetsOutputTypeToText(): void
    {
        $this->assertSame(OutputType::Text, ConvertRequest::toText(__DIR__.'/../../config/files/label.pdf')->getOutputType());
    }

    public function testSettingFirstPageGreaterThanLastPageClampsLastPageToFirstPageWhenLastPageIsNotNull(): void
    {
        $request = new ConvertRequest(__DIR__.'/../../config/files/label.pdf');

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

        $request = new ConvertRequest(__DIR__.'/../../config/files/label.pdf', $page, $page);
        $this->assertEquals($request->getLastPage(), $request->getFirstPage());

        $request->toPage($request->getLastPage() - 1);
        $this->assertEquals($request->getLastPage(), $request->getFirstPage());
    }
}
