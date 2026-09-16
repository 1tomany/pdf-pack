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

$pdfClient = new PdfClient($providers, Vendor::Poppler);

try {
    $file = $pdfClient->files->read($path);

    printf("The PDF file \"%s\" has %d %s.\n\n", $file->getName(), $file->getPageCount(), 1 === $file->getPageCount() ? 'page' : 'pages');

    // The generator converts each page only as it is requested.
    foreach ($pdfClient->files->convert($path, outputType: OutputType::Jpeg, resolution: 150) as $page) {
        printf("Page %d hash: %s\n", $page->getPage(), $page->getHash());
    }

    printf("\n");

    foreach ($pdfClient->files->convert($path, 3, 4, OutputType::Text) as $page) {
        printf("Page %d size: %d bytes\n", $page->getPage(), $page->getSize());
    }

    // Select another registered provider without changing the default facade.
    // $file = $pdfClient->use(Vendor::Mock)->files->read($path);
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
