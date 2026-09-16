# PDF Extraction Library for PHP

`pdf-pack` is a simple PHP library that makes page rasterization and text extraction from PDFs easy. It uses a single dependency, the [Symfony Process Component](https://symfony.com/doc/current/components/process.html), to interface with the [Poppler command line tools from the xpdf library](https://poppler.freedesktop.org/).

## Installation

Install the library using Composer:

```shell
composer require 1tomany/pdf-pack
```

## Installing Poppler

Before beginning, ensure the `pdfinfo`, `pdftoppm`, and `pdftotext` binaries are installed and located in your `$PATH`.

### macOS

```shell
brew install poppler
```

### Debian and Ubuntu

```shell
apt-get install poppler-utils
```

## Usage

This library has three main features:

- Read PDF metadata such as the page count
- Rasterize one or more pages to JPEG or PNG images
- Extract text from one or more pages

Extracted data is stored in memory and can be written to the filesystem or converted to an RFC2397 `data:` URL. Because extracted data is stored in memory, this library returns a `\Generator` object for each page that is extracted or rasterized.

The primary API is the `OneToMany\PdfPack\PdfClient` facade. It exposes PDF operations through its `files` resource and returns `File` and `Page` resources directly, though `Page` objects are wrapped in a `\Generator` to ensure they are lazily generated.

There are two ways to use the library: manually build the facade yourself and inject that where needed, or through an autowired and autoconfigured Symfony bundle.

### Facade usage

```php
<?php

use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\Resource\Registry;
use OneToMany\PdfPack\Vendor;

$providers = new Registry([
    new PopplerProvider(),
]);

$pdfClient = new PdfClient(Vendor::Poppler, $providers);

$pdf = $pdfClient->files->read('/path/to/file.pdf');

foreach ($pdfClient->files->convert($pdf->path, outputType: OutputType::Png, resolution: 150) as $page) {
    // $page is created only when the generator advances
}
```

The active provider can be changed fluently. Previously created resource objects are memoized, so switching back to a provider reuses its `Files` instance:

```php
$pdf = $pdfClient->use('mock')->files->read('/path/to/file.pdf');
```

See [`examples/facade.php`](https://github.com/1tomany/pdf-pack/blob/master/examples/facade.php) for a complete framework-independent example.

### Symfony integration

The package includes `OneToMany\PdfPack\PdfPackBundle`. Symfony Flex enables it automatically after installation, and `OneToMany\PdfPack\Contract\PdfClientInterface` can then be autowired without additional service configuration:

```php
<?php

use OneToMany\PdfPack\Contract\PdfClientInterface;

final readonly class ExtractPdf
{
    public function __construct(
        private PdfClientInterface $pdfClient,
    ) {
    }
}
```

Poppler is the default provider. Its configuration can be changed in `config/packages/onetomany_pdfpack.yaml`:

```yaml
onetomany_pdfpack:
    vendor: poppler
    poppler_provider:
        pdfinfo_binary: pdfinfo
        pdftoppm_binary: pdftoppm
        pdftotext_binary: pdftotext
```

The Symfony integration is optional at runtime: constructing `Registry` and `PdfClient` directly does not require a Symfony application or service container.

### Extensibility

This library uses the Poppler command line tools by default, but writing your own provider is simple.

```php
<?php

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;

final readonly class ImageMagickProvider implements ProviderInterface
{
    /**
     * @see OneToMany\PdfPack\Contract\Bridge\ProviderInterface
     *
     * @return 'imagick'
     */
    public static function getProvider(): string
    {
        return 'imagick';
    }

    /**
     * @see OneToMany\PdfPack\Contract\Bridge\ProviderInterface
     */
    #[\Override]
    public function convert(
        string $path,
        int $fromPage = 1,
        ?int $toPage = null,
        OutputType $outputType = OutputType::Jpeg,
        int $resolution = FilesInterface::DEFAULT_RESOLUTION,
    ): \Generator {
        // Use ImageMagick to rasterize pages and extract text
    }

    /**
     * @see OneToMany\PdfPack\Contract\Bridge\ProviderInterface
     */
    #[\Override]
    public function read(string $path): File
    {
        // Use ImageMagick to count the number of pages
    }
}
```

## Credits

- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License

The MIT License
