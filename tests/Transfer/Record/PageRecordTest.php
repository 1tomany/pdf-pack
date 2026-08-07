<?php

namespace OneToMany\PdfPack\Tests\Transfer\Record;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Transfer\Record\PageRecord;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function hash;
use function random_bytes;
use function random_int;
use function strlen;

#[Group('UnitTests')]
#[Group('TransferTests')]
#[Group('RecordTests')]
final class PageRecordTest extends TestCase
{
    public function testConstructorRequiresNonNegativePage(): void
    {
        $page = random_int(-100, -1);
        $this->assertLessThan(0, $page);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The page cannot be negative.');

        new PageRecord(OutputType::Text, 'PDF Page', $page);
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
        $path = __DIR__.'/../../../config/files/label.jpeg';
        $this->assertFileExists($path);

        $data = file_get_contents($path);
        $this->assertIsString($data);

        $pageRecord = PageRecord::asJpeg($data);
        $this->assertSame($data, $pageRecord->getData());

        $dataUri = 'data:image/jpeg;base64,'.base64_encode($data);
        $this->assertEquals($dataUri, $pageRecord->toDataUri());
    }
}
