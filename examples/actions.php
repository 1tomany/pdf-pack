<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Action\ConvertAction;
use OneToMany\PdfPack\Action\ReadAction;
use OneToMany\PdfPack\Client\Mock\MockClient;
use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface as PdfPackExceptionInterface;
use OneToMany\PdfPack\Factory\ClientContainer;
use OneToMany\PdfPack\Factory\ClientFactory;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
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
    $client = $clientFactory->create($vendor);

    // Read action to read PDF metadata
    $readAction = new ReadAction($client);

    // Convert action to convert PDF pages
    $convertAction = new ConvertAction($client);

    // Read PDF metadata
    $record = $readAction->act(new ReadRequest($path))->getRecord();

    printf("The PDF '%s' has %d %s.\n\n", $record->getName(), $record->getPageCount(), 1 === $record->getPageCount() ? 'page' : 'pages');

    // Convert all pages to 150 DPI JPEGs
    $convertRequest = ConvertRequest::toImage($path)->atResolution(150)->asJpegOutput();

    foreach ($convertAction->act($convertRequest) as $response) {
        printf("Page %d hash: %s\n", $response->getRecord()->getPage(), $response->getRecord()->getHash());
    }

    printf("\n");

    // Extract text from pages 3 and 4
    $convertToTextRequest = ConvertRequest::toText($path)->fromPage(3)->toPage(4);

    foreach ($convertAction->act($convertToTextRequest) as $response) {
        printf("Page %d size: %d %s\n", $response->getRecord()->getPage(), $response->getRecord()->getSize(), 1 === $response->getRecord()->getSize() ? 'byte' : 'bytes');
    }
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
