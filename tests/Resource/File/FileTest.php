<?php

namespace OneToMany\PdfPack\Tests\Resource\File;

use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Resource\File\File;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ResourceTests')]
final class FileTest extends TestCase
{
    public function testConstructingFile(): void
    {
        $file = new File('/path/to/file.pdf', 4);

        $this->assertSame('/path/to/file.pdf', $file->path);
        $this->assertSame('file.pdf', $file->name);
        $this->assertSame(4, $file->pageCount);
    }

    public function testPathCannotBeEmpty(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The path cannot be empty.');

        new File('', 1);
    }

    public function testPageCountCannotBeNegative(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIs('The page count cannot be negative.');

        new File('file.pdf', -1);
    }
}
