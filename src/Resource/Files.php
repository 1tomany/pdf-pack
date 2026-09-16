<?php

namespace OneToMany\PdfPack\Resource;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Exception\DomainException;
use OneToMany\PdfPack\Exception\RangeException;
use OneToMany\PdfPack\Resource\File\File;

use function is_file;
use function is_readable;
use function sprintf;
use function trim;

final readonly class Files implements FilesInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    /**
     * @see OneToMany\PdfPack\Contract\Resource\FilesInterface
     */
    #[\Override]
    public function convert(
        string $path,
        int $firstPage = 1,
        ?int $lastPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = 72,
    ): \Generator {
        $path = $this->assertPathIsValid($path);

        if ($firstPage < 1) {
            throw new DomainException('The first page must be greater than 0.');
        }

        if (null !== $lastPage) {
            if ($lastPage < 1) {
                throw new DomainException('The last page must be greater than 0.');
            }

            if ($lastPage < $firstPage) {
                throw new DomainException('The last page must be greater than or equal to the first page.');
            }
        }

        if ($resolution < self::MIN_RESOLUTION) {
            throw new RangeException(sprintf('The resolution must be %d DPI or larger.', self::MIN_RESOLUTION));
        }

        if ($resolution > self::MAX_RESOLUTION) {
            throw new RangeException(sprintf('The resolution must be %d DPI or smaller.', self::MAX_RESOLUTION));
        }

        return $this->provider->convert($path, $firstPage, $lastPage, $outputType, $resolution);
    }

    /**
     * @see OneToMany\PdfPack\Contract\Resource\FilesInterface
     */
    #[\Override]
    public function read(string $path): File
    {
        return $this->provider->read($this->assertPathIsValid($path));
    }

    /**
     * @return non-empty-string
     *
     * @throws DomainException when the path is empty
     * @throws DomainException when the path is not a readable file
     */
    private function assertPathIsValid(string $path): string
    {
        if ('' === $path = trim($path)) {
            throw new DomainException('The path cannot be empty.');
        }

        if (!is_file($path) || !is_readable($path)) {
            throw new DomainException(sprintf('The file "%s" is not readable.', $path));
        }

        return $path;
    }
}
