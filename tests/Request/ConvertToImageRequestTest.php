<?php

namespace OneToMany\PdfPack\Tests\Request;

use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\ConvertToImageRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class ConvertToImageRequestTest extends TestCase
{
    public function testConstructorRequiresOutputTypeToBeImage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The output type must be an image.');

        new ConvertToImageRequest(__DIR__.'/../../config/files/label.pdf', 1, 1, OutputType::Text);
    }
}
