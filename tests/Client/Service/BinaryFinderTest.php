<?php

namespace OneToMany\PdfPack\Tests\Client\Service;

use OneToMany\PdfPack\Client\Service\BinaryFinder;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ClientTests')]
#[Group('ServiceTests')]
final class BinaryFinderTest extends TestCase
{
    public function testFindingBinaryRequiresBinaryToExist(): void
    {
        $binary = \uniqid('binary_');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "'.$binary.'" could not be found.');

        BinaryFinder::find($binary);
    }
}
