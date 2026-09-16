<?php

namespace OneToMany\PdfPack\Bridge\Trait;

use OneToMany\PdfPack\Exception\DomainException;
use Symfony\Component\Process\ExecutableFinder;

use function is_executable;
use function sprintf;

trait BinaryTrait
{
    /**
     * @throws DomainException when the binary could not be found
     */
    private function findBinary(string $binary): string
    {
        if (is_executable($binary)) {
            return $binary;
        }

        if (null === $binaryPath = new ExecutableFinder()->find($binary)) {
            throw new DomainException(sprintf('The binary "%s" could not be found.', $binary));
        }

        return $binaryPath;
    }
}
