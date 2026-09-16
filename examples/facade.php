<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Bridge\Mock\MockProvider;
use OneToMany\PdfPack\Bridge\Poppler\PopplerProvider;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface as PdfPackExceptionInterface;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\Resource\Registry;
use OneToMany\PdfPack\Vendor;

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/../data/files/s3.pdf');

$providers = new Registry([
    new MockProvider(),
    new PopplerProvider(),
]);

$pdfClient = new PdfClient(Vendor::Poppler, $providers);

try {
    $file = $pdfClient->files->read($path);

    vprintf("The PDF file \"%s\" has %d pages.\n\n", [
        $file->getName(), $file->getPageCount(),
    ]);

    printf("Converting all pages to 150 DPI JPEG images:\n\n");

    // The generator converts each page only as it is requested.
    $pages = $pdfClient->files->convert($path, 1, resolution: 150);

    foreach ($pages as $page) {
        printf("Page #%d hash: %s\n", $page->getPage(), $page->getHash());
    }

    printf("\n");
    printf("Extracting text from pages 3 and 4:\n\n");

    foreach ($pdfClient->files->convert($path, 3, 4, OutputType::Text) as $page) {
        printf("Page #%d text size: %d bytes\n", $page->getPage(), $page->getSize());
    }

    // Switch the facade to another registered provider.
    $file = $pdfClient->use(Vendor::Mock)->files->read($path);
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
