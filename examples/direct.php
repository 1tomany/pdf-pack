<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface as PdfPackExceptionInterface;
use OneToMany\PdfPack\Request\ExtractPdfRequest;
use OneToMany\PdfPack\Request\ReadPdfRequest;

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/.data/s3.pdf');
assert(is_file($path) && is_readable($path));

$client = new PopplerClient();

try {
    $response = $client->read(new ReadPdfRequest($path));
    printf("The PDF '%s' has %d %s.\n\n", basename($path), $response->getPages(), 1 === $response->getPages() ? 'page' : 'pages');

    // Convert all pages to 150 DPI JPEGs
    $extractPdfRequest = new ExtractPdfRequest($path)->fromPage(1)->asJpegOutput()->atResolution(150);

    foreach ($client->extract($extractPdfRequest) as $page) {
        printf("Page %d MD5 hash: %s\n", $page->getPage(), md5($page->getData()));
    }

    echo "\n";

    // Extract text from pages 3 and 4
    $extractPdfRequest = new ExtractPdfRequest($path)->fromPage(3)->toPage(4)->asTextOutput();

    foreach ($client->extract($extractPdfRequest) as $page) {
        printf("Page %d length: %d\n", $page->getPage(), strlen($page->getData()));
    }
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
