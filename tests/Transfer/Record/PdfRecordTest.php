<?php

namespace OneToMany\PdfPack\Tests\Transfer\Record;

use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function random_int;

#[Group('UnitTests')]
#[Group('TransferTests')]
#[Group('RecordTests')]
final class PdfRecordTest extends TestCase
{
    public function testConstructorRequiresNonEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The path cannot be empty.');

        new PdfRecord('', random_int(1, 100));
    }

    public function testConstructorRequiresNonNegativePageCount(): void
    {
        $pageCount = random_int(-100, -1);
        $this->assertLessThan(0, $pageCount);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The page count cannot be negative.');

        new PdfRecord('file.pdf', $pageCount);
    }
}
