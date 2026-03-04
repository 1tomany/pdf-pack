<?php

namespace OneToMany\PdfPack\Tests\Request;

use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\ReadMetadataRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class ReadMetadataRequestTest extends TestCase
{
    public function testConstructorRequiresReadableFile(): void
    {
        $filePath = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($filePath);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file path "'.$filePath.'" does not exist or is not readable.');

        new ReadMetadataRequest($filePath);
    }
}
