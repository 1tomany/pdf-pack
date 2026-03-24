<?php

namespace OneToMany\PdfPack\Tests\Request;

use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class ReadPdfRequestTest extends TestCase
{
    public function testConstructorRequiresNonEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The path cannot be empty.');

        new ReadPdfRequest('');
    }

    public function testConstructorRequiresReadableFile(): void
    {
        $path = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($path);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file "'.$path.'" is not readable.');

        new ReadPdfRequest($path);
    }
}
