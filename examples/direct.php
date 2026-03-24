<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface as PdfPackExceptionInterface;
use OneToMany\PdfPack\Request\ConvertToImageRequest;
use OneToMany\PdfPack\Request\ConvertToTextRequest;
use OneToMany\PdfPack\Request\ReadPdfRequest;

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/.data/s3.pdf');

$popplerClient = new PopplerClient();

try {
    // Read PDF metadata
    $response = $popplerClient->read(...[
        'request' => new ReadPdfRequest($path),
    ]);

    printf("The PDF '%s' has %d %s.\n\n", $response->getName(), $response->getPages(), 1 === $response->getPages() ? 'page' : 'pages');

    // Convert all pages to 150 DPI JPEGs
    $convertToImageRequest = new ConvertToImageRequest($path)->fromPage(1)->atResolution(150)->asJpegOutput();

    foreach ($popplerClient->convert($convertToImageRequest) as $page) {
        printf("Page %d hash: %s\n", $page->getPage(), $page->getHash());
    }

    echo "\n";

    // Extract text from pages 3 and 4
    $convertToTextRequest = new ConvertToTextRequest($path)->fromPage(3)->toPage(4);

    foreach ($popplerClient->convert($convertToTextRequest) as $page) {
        printf("Page %d size: %d bytes\n", $page->getPage(), $page->getSize());
    }
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
