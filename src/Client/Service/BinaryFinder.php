<?php

namespace OneToMany\PdfPack\Client\Service;

use OneToMany\PdfPack\Exception\InvalidArgumentException;
use Symfony\Component\Process\ExecutableFinder;

use function is_executable;

final readonly class BinaryFinder
{
    private function __construct()
    {
    }

    public static function find(string $binary): string
    {
        if (is_executable($binary)) {
            return $binary;
        }

        if (null === $binaryPath = new ExecutableFinder()->find($binary)) {
            throw new InvalidArgumentException(sprintf('The binary "%s" could not be found.', $binary));
        }

        return $binaryPath;
    }
}
