<?php

namespace OneToMany\PdfPack\Tests\Factory;

use OneToMany\PdfPack\Factory\ClientContainer;
use OneToMany\PdfPack\Factory\Exception\ContainerEntryNotFoundException;
use PHPUnit\Framework\TestCase;

final class ClientContainerTest extends TestCase
{
    public function testGettingEntryRequiresEntryToExist(): void
    {
        $this->expectException(ContainerEntryNotFoundException::class);
        $this->expectExceptionMessage('The entry "invalid" was not found in the container.');

        new ClientContainer()->get('invalid');
    }
}
