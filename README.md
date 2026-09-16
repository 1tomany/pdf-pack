# PDF Extraction Library for PHP

`pdf-pack` is a simple PHP library that makes page rasterization and text extraction from PDFs easy. It uses the [Symfony Process Component](https://symfony.com/doc/current/components/process.html) to interface with the [Poppler command line tools from the xpdf library](https://poppler.freedesktop.org/) and includes Symfony bundle integration for autowiring and configuration.

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

$providers = new Registry([
    new PopplerProvider(),
]);

$pdfClient = new PdfClient('poppler', $providers);

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
    provider: poppler
    poppler_provider:
        pdfinfo_binary: pdfinfo
        pdftoppm_binary: pdftoppm
        pdftotext_binary: pdftotext
```

### Extensibility

This library uses the Poppler command line tools by default, but provider names are strings, so writing your own integration is simple.

```php
<?php

use OneToMany\PdfPack\Contract\Bridge\ProviderInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Resource\FilesInterface;
use OneToMany\PdfPack\Resource\File\File;

final readonly class ImagickProvider implements ProviderInterface
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

    // Implement the convert() and read() methods to satisfy the interface
}
```

Services implementing `ProviderInterface` are tagged automatically in Symfony applications. The custom provider can then be selected normally:

```yaml
onetomany_pdfpack:
    provider: imagick
```

The Symfony integration is optional at runtime: constructing `Registry` and `PdfClient` directly does not require a Symfony application or service container.

## Credits

- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License

The MIT License
