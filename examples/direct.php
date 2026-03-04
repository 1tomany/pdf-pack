<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface;
use OneToMany\PdfPack\Request\ExtractRequest;
use OneToMany\PdfPack\Request\ReadRequest;

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/data/pages-4.pdf');
assert(is_file($path) && is_readable($path));

$client = new PopplerClient();

try {
    $response = $client->read(new ReadRequest($path));
    printf("The PDF '%s' has %d %s.\n\n", basename($path), $response->getPages(), 1 === $response->getPages() ? 'page' : 'pages');

    // Rasterize all pages as 150 DPI JPEGs
    $extractRequest = new ExtractRequest($path)->fromPage(1)->asJpegOutput()->atResolution(150);

    foreach ($client->extract($extractRequest) as $page) {
        printf("Page %d MD5 hash: %s\n", $page->getPage(), md5($page->getData()));
    }

    echo "\n";

    // Extract text from pages 3 and 4
    $extractRequest = new ExtractRequest($path)->fromPage(3)->toPage(4)->asTextOutput();

    foreach ($client->extract($extractRequest) as $page) {
        printf("Page %d length: %d\n", $page->getPage(), strlen($page->getData()));
    }
} catch (ExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
