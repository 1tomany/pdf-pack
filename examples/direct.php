<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface as PdfPackExceptionInterface;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/../config/files/s3.pdf');

$popplerClient = new PopplerClient();

try {
    // Read PDF metadata
    $record = $popplerClient->read(new ReadRequest($path));

    printf("The PDF '%s' has %d %s.\n\n", $record->getName(), $record->getPageCount(), 1 === $record->getPageCount() ? 'page' : 'pages');

    // Convert all pages to 150 DPI JPEGs
    $convertRequest = ConvertRequest::toImage($path)->atResolution(150)->asJpegOutput();

    foreach ($popplerClient->convert($convertRequest) as $page) {
        printf("Page %d hash: %s\n", $page->getPage(), $page->getHash());
    }

    echo "\n";

    // Extract text from pages 3 and 4
    $convertToTextRequest = ConvertRequest::toText($path)->fromPage(3)->toPage(4);

    foreach ($popplerClient->convert($convertToTextRequest) as $page) {
        printf("Page %d size: %d bytes\n", $page->getPage(), $page->getSize());
    }
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
