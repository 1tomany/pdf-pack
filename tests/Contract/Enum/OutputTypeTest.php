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
     * @return list<list<non-empty-string|OutputType>>
     */
    public static function providerGettingExtension(): array
    {
        $provider = [
            [OutputType::Jpg, 'jpeg'],
            [OutputType::Png, 'png'],
            [OutputType::Txt, 'txt'],
        ];

        return $provider;
    }

    #[DataProvider('providerGettingMimeType')]
    public function testGettingMimeType(OutputType $type, string $mimeType): void
    {
        $this->assertEquals($mimeType, $type->getMimeType());
    }

    /**
     * @return list<list<non-empty-string|OutputType>>
     */
    public static function providerGettingMimeType(): array
    {
        $provider = [
            [OutputType::Jpg, 'image/jpeg'],
            [OutputType::Png, 'image/png'],
            [OutputType::Txt, 'text/plain'],
        ];

        return $provider;
    }
}
