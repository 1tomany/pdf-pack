<?php

namespace OneToMany\PdfPack\Tests\Response;

use OneToMany\PdfPack\Response\ReadPdfResponse;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function random_int;

#[Group('UnitTests')]
#[Group('ResponseTests')]
final class ReadResponseTest extends TestCase
{
    public function testConstructorClampsNonPositiveNonZeroPagesValuesToOne(): void
    {
        $this->assertEquals(1, new ReadPdfResponse('/path/to/file.pdf', -1 * random_int(1, 100))->getPages());
    }
}
