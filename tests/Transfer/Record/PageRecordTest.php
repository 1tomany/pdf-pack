<?php

namespace OneToMany\PdfPack\Tests\Transfer\Record;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Transfer\Record\PageRecord;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function hash;
use function random_bytes;
use function random_int;
use function strlen;

#[Group('UnitTests')]
#[Group('TransferTests')]
#[Group('RecordTests')]
final class PageRecordTest extends TestCase
{
    public function testConstructorRequiresStrictlyPositivePage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The page must be greater than 0.');

        new PageRecord(OutputType::Text, 'PDF Page', 0);
    }

    public function testConstructorCalculatesPageHash(): void
    {
        $data = random_bytes(1024);
        $hash = hash('sha256', $data);

        $pageRecord = PageRecord::asJpeg($data);
        $this->assertSame($hash, $pageRecord->getHash());
    }

    public function testConstructorCalculatesPageSize(): void
    {
        $size = random_int(32, 1024);
        $this->assertGreaterThan(0, $size);

        $data = random_bytes($size);
        $this->assertSame($size, strlen($data));

        $pageRecord = PageRecord::asJpeg($data);
        $this->assertSame($size, $pageRecord->getSize());
    }

    #[DataProvider('providerTypePageAndName')]
    public function testGettingName(
        OutputType $type,
        int $page,
        string $name,
    ): void {
        $this->assertEquals($name, new PageRecord($type, random_bytes(1024), $page)->getName());
    }

    /**
     * @return list<array{OutputType,int,string}>
     */
    public static function providerTypePageAndName(): array
    {
        $provider = [
            [OutputType::Jpeg, 1, 'page-1.jpeg'],
            [OutputType::Jpeg, 10, 'page-10.jpeg'],
            [OutputType::Png, 5, 'page-5.png'],
            [OutputType::Text, 5, 'page-5.txt'],
        ];

        return $provider;
    }
}
