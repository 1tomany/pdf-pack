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
    #[DataProvider('providerGettingExtension')]
    public function testGettingExtension(OutputType $type, string $extension): void
    {
        $this->assertEquals($extension, $type->getExtension());
    }

    /**
     * @return list<list<non-empty-lowercase-string|OutputType>>
     */
    public static function providerGettingExtension(): array
    {
        $provider = [
            [OutputType::Jpeg, 'jpeg'],
            [OutputType::Png, 'png'],
            [OutputType::Text, 'txt'],
        ];

        return $provider;
    }

    #[DataProvider('providerGettingFormat')]
    public function testGettingFormat(OutputType $type, string $format): void
    {
        $this->assertEquals($format, $type->getFormat());
    }

    /**
     * @return list<list<non-empty-lowercase-string|OutputType>>
     */
    public static function providerGettingFormat(): array
    {
        $provider = [
            [OutputType::Jpeg, 'image/jpeg'],
            [OutputType::Png, 'image/png'],
            [OutputType::Text, 'text/plain'],
        ];

        return $provider;
    }
}
