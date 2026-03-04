<?php

namespace OneToMany\PdfPack\Tests\Response;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Response\ExtractedDataResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function file_get_contents;
use function random_int;

#[Group('UnitTests')]
#[Group('ResponseTests')]
final class ExtractedDataResponseTest extends TestCase
{
    public function testToString(): void
    {
        $text = 'Hello, world!';

        $this->assertEquals($text, new ExtractedDataResponse(OutputType::Text, $text)->__toString());
    }

    public function testGettingPageClampsNonPositiveNonZeroValuesToOne(): void
    {
        $response = new ExtractedDataResponse(
            OutputType::Text, 'Hello, world!'
        );

        $property = new \ReflectionProperty($response, 'page');

        // Page is zero
        $property->setValue($response, 0);
        $this->assertEquals(1, $response->getPage());

        // Page is negative
        $page = -1 * random_int(1, 100);
        $this->assertLessThan(1, $page);

        $property->setValue($response, $page);
        $this->assertEquals(1, $response->getPage());

        // Page is greater than one
        $page = random_int(2, 100);
        $this->assertGreaterThan(1, $page);

        $property->setValue($response, $page);
        $this->assertEquals($page, $response->getPage());
    }

    public function testSettingPageClampsNonPositiveNonZeroValuesToOne(): void
    {
        $response = new ExtractedDataResponse(
            OutputType::Text, 'Hello, world!'
        );

        $this->assertEquals(1, $response->getPage());

        // Page is zero
        $response->setPage(0);
        $this->assertEquals(1, $response->getPage());

        // Page is negative
        $page = -1 * random_int(1, 100);
        $this->assertLessThan(1, $page);

        $response->setPage($page);
        $this->assertEquals(1, $response->getPage());

        // Page is greater than one
        $page = random_int(2, 100);
        $this->assertGreaterThan($response->getPage(), $page);

        $response->setPage($page);
        $this->assertEquals($page, $response->getPage());
    }

    #[DataProvider('providerGettingName')]
    public function testGettingName(
        OutputType $type,
        int $page,
        string $name,
    ): void {
        $this->assertEquals($name, new ExtractedDataResponse($type, '', $page)->getName());
    }

    /**
     * @return list<list<positive-int|non-empty-string|OutputType>>
     */
    public static function providerGettingName(): array
    {
        $page = random_int(1, 100);

        $provider = [
            [OutputType::Jpeg, $page, "page-{$page}.jpeg"],
            [OutputType::Png, $page, "page-{$page}.png"],
            [OutputType::Text, $page, "page-{$page}.txt"],
        ];

        return $provider;
    }

    public function testToDataUri(): void
    {
        $filePath = __DIR__.'/files/page.png';
        $this->assertFileExists($filePath);

        $data = file_get_contents($filePath);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);

        $dataUri = 'data:image/png;base64,'.base64_encode($data);
        $this->assertEquals($dataUri, new ExtractedDataResponse(OutputType::Png, $data)->toDataUri());
    }
}
