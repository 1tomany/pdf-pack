<?php

namespace OneToMany\PdfPack\Tests\Exception;

use OneToMany\PdfPack\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
final class RuntimeExceptionTest extends TestCase
{
    public function testCreatingBinaryProcessFailureUsesFirstErrorLine(): void
    {
        $exception = RuntimeException::binaryProcessFailed('The process failed.', "First error.\nSecond error.");

        $this->assertSame('The process failed: First error.', $exception->getMessage());
    }

    public function testCreatingReadingFailure(): void
    {
        $exception = RuntimeException::readingPdfFailed('/file.pdf', 'Invalid PDF');

        $this->assertSame('Reading the file "/file.pdf" failed: Invalid PDF.', $exception->getMessage());
    }

    public function testCreatingConversionFailure(): void
    {
        $previous = new \RuntimeException();
        $exception = RuntimeException::convertingPdfFailed('/file.pdf', 2, previous: $previous);

        $this->assertSame('Converting page 2 of the file "/file.pdf" failed.', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
