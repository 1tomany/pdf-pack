<?php

namespace OneToMany\PdfPack\Bridge\Mock;

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Resource\File\File;
use OneToMany\PdfPack\Resource\File\Page;
use OneToMany\PdfPack\Vendor;

use function random_int;

final readonly class MockProvider implements ProviderInterface
{
    #[\Override]
    public static function getVendor(): Vendor
    {
        return Vendor::Mock;
    }

    /**
     * @return \Generator<int, Page>
     */
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
        return new File($path, random_int(1, 100));
    }
}
