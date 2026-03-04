<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Client\Poppler\PopplerExtractorClient;
use OneToMany\PdfPack\Request\ExtractRequest;
use OneToMany\PdfPack\Request\ReadRequest;

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/data/pages-4.pdf');
assert(is_file($path) && is_readable($path));

$client = new PopplerExtractorClient();

$response = $client->read(new ReadRequest($path));
printf("The PDF '%s' has %d page(s).\n", $path, $response->getPages());

// Rasterize all pages as 150 DPI JPEGs
$extractRequest = new ExtractRequest($path)
    ->fromPage(1)
    ->asJpegOutput()
    ->atResolution(150);

foreach ($client->extract($extractRequest) as $image) {
    printf("MD5: %s\n", md5($image->getData()));
}

// Extract text from pages 3 and 4
$extractRequest = new ExtractRequest($path)
    ->fromPage(3)
    ->toPage(4)
    ->asTextOutput();

foreach ($client->extract($extractRequest) as $text) {
    printf("Length: %d\n", strlen($text->getData()));
}
