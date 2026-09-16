<?php

namespace OneToMany\PdfPack\Tests\Fixture\Bridge;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;

final readonly class ImagickProvider implements ProviderInterface
{
    /**
     * @return 'imagick'
     */
    #[\Override]
    public static function getProvider(): string
    {
        return 'imagick';
    }

    #[\Override]
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = FilesInterface::DEFAULT_RESOLUTION,
    ): \Generator {
        $toPage ??= $fromPage;

        for ($page = $fromPage; $page <= $toPage; ++$page) {
            yield new Page($outputType, '', $page);
        }
    }

    #[\Override]
    public function read(string $path): File
    {
        return new File($path, 1);
    }
}
