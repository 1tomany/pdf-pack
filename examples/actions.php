<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Action\ConvertPdfAction;
use OneToMany\PdfPack\Action\ReadPdfAction;
use OneToMany\PdfPack\Client\Mock\MockClient;
use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface as PdfPackExceptionInterface;
use OneToMany\PdfPack\Factory\ClientContainer;
use OneToMany\PdfPack\Factory\ClientFactory;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ConvertToTextRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;

// Changing this to 'mock' would use the
// MockClient with *no* further changes
$vendor = 'poppler';

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/../config/files/s3.pdf');

$clientContainer = new ClientContainer([
    new MockClient(),
    new PopplerClient(),
]);

$clientFactory = new ClientFactory($clientContainer);

try {
    // Create read and extract actions
    $readPdfAction = new ReadPdfAction(...[
        'client' => $clientFactory->create($vendor),
    ]);

    $convertPdfAction = new ConvertPdfAction(...[
        'client' => $clientFactory->create($vendor),
    ]);

    // Read PDF metadata
    $response = $readPdfAction->act(new ReadRequest($path));

    printf("The PDF '%s' has %d %s.\n\n", $response->getName(), $response->getPages(), 1 === $response->getPages() ? 'page' : 'pages');

    // Convert all pages to 150 DPI JPEGs
    $convertToImageRequest = ConvertRequest::toImage($path)->fromPage(1)->atResolution(150)->asJpegOutput();

    foreach ($convertPdfAction->act($convertToImageRequest) as $page) {
        printf("Page %d hash: %s\n", $page->getPage(), $page->getHash());
    }

    echo "\n";

    // Extract text from pages 3 and 4
    $convertToTextRequest = new ConvertToTextRequest($path)->fromPage(3)->toPage(4);

    foreach ($convertPdfAction->act($convertToTextRequest) as $page) {
        printf("Page %d size: %d bytes\n", $page->getPage(), $page->getSize());
    }
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
