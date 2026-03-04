<?php

namespace OneToMany\PdfPack\Tests\Response;

use OneToMany\PdfPack\Response\ReadResponse;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function random_int;

#[Group('UnitTests')]
#[Group('ResponseTests')]
final class MetadataResponseTest extends TestCase
{
    public function testSettingPagesClampsNonPositiveNonZeroValuesToOne(): void
    {
        $this->assertEquals(1, new ReadResponse()->setPages(-1 * random_int(1, 100))->getPages());
    }
}
