<?php

namespace OneToMany\PdfPack\Tests\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Request\ExtractDataRequestInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\Data\ExtractRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function random_int;

use const PHP_INT_MAX;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class ExtractDataRequestTest extends TestCase
{
    private ?string $filePath = null;

    protected function setUp(): void
    {
        $this->filePath = __DIR__.'/files/label.pdf';
    }

    public function testConstructorRequiresNonEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file path cannot be empty.');

        new ExtractRequest('');
    }

    public function testConstructorRequiresReadableFile(): void
    {
        $filePath = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($filePath);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file path "'.$filePath.'" does not exist or is not readable.');

        new ExtractRequest($filePath);
    }

    public function testConstructorRequiresPositiveNonZeroFirstPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The first page number must be a positive non-zero integer.');

        new ExtractRequest($this->filePath, firstPage: 0);
    }

    public function testConstructorRequiresPositiveNonZeroLastPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The last page number must be a positive non-zero integer.');

        new ExtractRequest($this->filePath, lastPage: 0);
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMinimumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between '.ExtractDataRequestInterface::MIN_RESOLUTION.' and '.ExtractDataRequestInterface::MAX_RESOLUTION.'.');

        new ExtractRequest($this->filePath, resolution: random_int(1, ExtractDataRequestInterface::MIN_RESOLUTION - 1));
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMaximumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between '.ExtractDataRequestInterface::MIN_RESOLUTION.' and '.ExtractDataRequestInterface::MAX_RESOLUTION.'.');

        new ExtractRequest($this->filePath, resolution: random_int(ExtractDataRequestInterface::MAX_RESOLUTION + 1, PHP_INT_MAX));
    }

    #[DataProvider('providerConstructor')]
    public function testConstructor(
        string $filePath,
        int $firstPage,
        int $lastPage,
        OutputType $outputType,
        int $resolution,
    ): void {
        $request = new ExtractRequest($filePath, $firstPage, $lastPage, $outputType, $resolution);

        $this->assertEquals($filePath, $request->getFilePath());
        $this->assertEquals($firstPage, $request->getFirstPage());
        $this->assertEquals($lastPage, $request->getLastPage());
        $this->assertEquals($outputType, $request->getOutputType());
        $this->assertEquals($resolution, $request->getResolution());
    }

    /**
     * @return list<list<int|string|OutputType>>
     */
    public static function providerConstructor(): array
    {
        $resolution = random_int(
            ExtractDataRequestInterface::MIN_RESOLUTION,
            ExtractDataRequestInterface::MAX_RESOLUTION,
        );

        $provider = [
            [__DIR__.'/files/label.pdf', 1, 1, OutputType::Png, $resolution],
            [__DIR__.'/files/label.pdf', 2, 4, OutputType::Jpeg, $resolution],
        ];

        return $provider;
    }

    public function testSettingFirstPageGreaterThanLastPageClampsLastPageToFirstPage(): void
    {
        $request = new ExtractRequest($this->filePath);
        $this->assertEquals($request->getFirstPage(), $request->getLastPage());

        $firstPage = $request->getFirstPage() + 1;
        $this->assertGreaterThan($request->getLastPage(), $firstPage);

        $request->setFirstPage($firstPage);
        $this->assertEquals($request->getFirstPage(), $request->getLastPage());
    }

    public function testSettingLastPageLessThanLastFirstClampsFirstPageToLastPage(): void
    {
        $page = random_int(2, 10);

        $request = new ExtractRequest($this->filePath, $page, $page);
        $this->assertEquals($request->getLastPage(), $request->getFirstPage());

        $lastPage = $request->getLastPage() - 1;
        $this->assertLessThan($request->getFirstPage(), $lastPage);

        $request->toPage($lastPage);
        $this->assertEquals($request->getLastPage(), $request->getFirstPage());
    }
}
