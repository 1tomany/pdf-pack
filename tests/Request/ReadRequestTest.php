<?php

namespace OneToMany\PdfPack\Tests\Request;

use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class ReadRequestTest extends TestCase
{
    public function testConstructorRequiresNonEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The path cannot be empty.');

        new ReadRequest('');
    }

    public function testConstructorRequiresReadableFile(): void
    {
        $path = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($path);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The file "'.$path.'" is not readable.');

        new ReadRequest($path);
    }
}
