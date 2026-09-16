# PDF Extraction Library for PHP

`pdf-pack` is a simple PHP library that makes rasterizing pages and extracting text from PDFs for large language models easy. It uses a single dependency, the [Symfony Process Component](https://symfony.com/doc/current/components/process.html), to interface with the [Poppler command line tools from the xpdf library](https://poppler.freedesktop.org/).

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

- Read PDF metadata such as the number of pages
- Rasterize one or more pages to JPEG or PNG images
- Extract text from one or more pages

Extracted data is stored in memory and can be written to the filesystem or converted to a `data:` URI. Because extracted data is stored in memory, this library returns a `\Generator` object for each page that is extracted or rasterized.

The primary API is the `OneToMany\PdfPack\PdfClient` facade. It exposes PDF operations through its `files` resource and returns `File` and `Page` resources directly. `convert()` always returns a `Generator`, so page conversion remains lazy.

**Note:** A [Symfony bundle](https://github.com/1tomany/pdf-pack-bundle) is available if you wish to integrate this library into your Symfony applications with autowiring and configuration support.

### Facade usage

```php
use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\Resource\Registry;

$providers = new Registry([
    new PopplerProvider(),
]);

$pdfClient = new PdfClient($providers, 'poppler');

$pdf = $pdfClient->files->read('/path/to/file.pdf');

foreach ($pdfClient->files->convert('/path/to/file.pdf', outputType: OutputType::Png, resolution: 150) as $page) {
    // $page is converted only when the generator advances.
}
```

The configured provider can be changed for one chain without mutating the original facade:

```php
$pdf = $pdfClient->use('mock')->files->read('/path/to/file.pdf');
```

The direct `$pdfClient->read()` and `$pdfClient->convert()` methods are convenience aliases for the corresponding `files` methods. See [`examples/facade.php`](https://github.com/1tomany/pdf-pack/blob/master/examples/facade.php) for a complete example.

## Credits

- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License

The MIT License
