<?php

namespace OneToMany\PdfPack\Tests\Contract\Enum;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ContractTests')]
#[Group('EnumTests')]
final class OutputTypeTest extends TestCase
{
    #[DataProvider('providerOutputTypeAndExtension')]
    public function testGettingExtension(OutputType $type, string $extension): void
    {
        $this->assertEquals($extension, $type->getExtension());
    }

    /**
     * @return list<list<non-empty-lowercase-string|OutputType>>
     */
    public static function providerOutputTypeAndExtension(): array
    {
        $provider = [
            [OutputType::Jpeg, 'jpeg'],
            [OutputType::Png, 'png'],
            [OutputType::Text, 'txt'],
        ];

        return $provider;
    }

    #[DataProvider('providerOutputTypeAndFormat')]
    public function testGettingFormat(OutputType $type, string $format): void
    {
        $this->assertEquals($format, $type->getFormat());
    }

    /**
     * @return list<list<non-empty-lowercase-string|OutputType>>
     */
    public static function providerOutputTypeAndFormat(): array
    {
        $provider = [
            [OutputType::Jpeg, 'image/jpeg'],
            [OutputType::Png, 'image/png'],
            [OutputType::Text, 'text/plain'],
        ];

        return $provider;
    }

    public function testIsJpeg(): void
    {
        $this->assertTrue(OutputType::Jpeg->isJpeg()); // @phpstan-ignore-line
    }

    public function testIsPng(): void
    {
        $this->assertTrue(OutputType::Png->isPng()); // @phpstan-ignore-line
    }

    public function testIsText(): void
    {
        $this->assertTrue(OutputType::Text->isText()); // @phpstan-ignore-line
    }
}
