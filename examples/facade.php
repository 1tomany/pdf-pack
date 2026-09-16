<?php

require_once __DIR__.'/../vendor/autoload.php';

use OneToMany\PdfPack\Client\Mock\MockClient;
use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Enum\Vendor;
use OneToMany\PdfPack\Contract\Exception\ExceptionInterface as PdfPackExceptionInterface;
use OneToMany\PdfPack\Factory\ClientContainer;
use OneToMany\PdfPack\Factory\ClientFactory;
use OneToMany\PdfPack\PdfClient;

/** @var non-empty-string $path */
$path = realpath(__DIR__.'/../data/files/s3.pdf');

$clients = new ClientFactory(new ClientContainer([
    new MockClient(),
    new PopplerClient(),
]));

$pdfClient = new PdfClient($clients, Vendor::Poppler);

try {
    $pdf = $pdfClient->files->read($path);

    printf("The PDF '%s' has %d %s.\n\n", $pdf->getName(), $pdf->getPageCount(), 1 === $pdf->getPageCount() ? 'page' : 'pages');

    // The generator converts each page only as it is requested.
    foreach ($pdfClient->files->convert($path, outputType: OutputType::Jpeg, resolution: 150) as $page) {
        printf("Page %d hash: %s\n", $page->getPage(), $page->getHash());
    }

    printf("\n");

    foreach ($pdfClient->files->convert($path, 3, 4, OutputType::Text) as $page) {
        printf("Page %d size: %d bytes\n", $page->getPage(), $page->getSize());
    }

    // Select another registered client without changing the default facade.
    $mockPdf = $pdfClient->use(Vendor::Mock)->files->read($path);
} catch (PdfPackExceptionInterface $e) {
    printf("[ERROR] %s\n", $e->getMessage());
}
