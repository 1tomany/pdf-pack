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

Using the library is easy, and you have two ways to interact with it:

1. **Direct** Instantiate the `OneToMany\PdfPack\Client\Poppler\PopplerClient` class and call the methods directly. This method is easier to use, but comes with the cost that your application will be less flexible and testable.
2. **Actions** Create a container of `OneToMany\PdfPack\Contract\Client\ClientInterface` objects, and use the `OneToMany\PdfPack\Factory\ClientFactory` class to instantiate them.

**Note:** A [Symfony bundle](https://github.com/1tomany/pdf-pack-bundle) is available if you wish to integrate this library into your Symfony applications with autowiring and configuration support.

### Direct usage

See [`examples/direct.php`](https://github.com/1tomany/pdf-pack/blob/master/examples/direct.php).

## Credits

- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License

The MIT License
