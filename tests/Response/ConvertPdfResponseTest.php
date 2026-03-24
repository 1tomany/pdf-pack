<?php

namespace OneToMany\PdfPack\Tests\Response;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Response\ConvertPdfResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function file_get_contents;
use function random_int;

#[Group('UnitTests')]
#[Group('ResponseTests')]
final class ConvertPdfResponseTest extends TestCase
{
    public function testToString(): void
    {
        $this->assertEquals('Hello, world!', new ConvertPdfResponse(OutputType::Text, 'Hello, world!')->__toString());
    }

    /**
     * @param positive-int $page
     */
    #[DataProvider('providerOutputTypePageNumberAndName')]
    public function testGettingName(OutputType $type, int $page, string $name): void
    {
        $this->assertEquals($name, new ConvertPdfResponse($type, '', $page)->getName());
    }

    /**
     * @return list<list<positive-int|non-empty-string|OutputType>>
     */
    public static function providerOutputTypePageNumberAndName(): array
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
        $path = __DIR__.'/../.data/label.jpeg';
        $this->assertFileExists($path);

        $data = file_get_contents($path);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);

        $dataUri = 'data:image/jpeg;base64,'.base64_encode($data);
        $this->assertEquals($dataUri, new ConvertPdfResponse(OutputType::Jpeg, $data)->toDataUri());
    }
}
