<?php

namespace OneToMany\PdfPack\Tests\Client\Poppler;

use OneToMany\PdfPack\Client\Exception\ExtractingDataFailedException;
use OneToMany\PdfPack\Client\Exception\ReadingMetadataFailedException;
use OneToMany\PdfPack\Client\Poppler\PopplerExtractorClient;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Response\ExtractedDataResponseInterface;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\ExtractDataRequest;
use OneToMany\PdfPack\Request\ExtractTextRequest;
use OneToMany\PdfPack\Request\ReadMetadataRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function iterator_to_array;
use function md5;
use function random_int;

#[Large]
#[Group('UnitTests')]
#[Group('ClientTests')]
#[Group('PopplerTests')]
final class PopplerExtractorClientTest extends TestCase
{
    public function testReadingMetadataRequiresValidPdfInfoBinary(): void
    {
        $pdfInfoBinary = 'invalid_pdfinfo_binary';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "'.$pdfInfoBinary.'" could not be found.');

        new PopplerExtractorClient(pdfInfoBinary: $pdfInfoBinary)->readMetadata(new ReadMetadataRequest(__FILE__));
    }

    public function testReadingMetadataRequiresValidPdfFile(): void
    {
        $this->expectException(ReadingMetadataFailedException::class);
        $this->expectExceptionMessageMatches('/May not be a PDF file/');

        new PopplerExtractorClient()->readMetadata(new ReadMetadataRequest(__FILE__));
    }

    #[DataProvider('providerReadingMetadata')]
    public function testReadingMetadata(string $filePath, int $pages): void
    {
        $client = new PopplerExtractorClient();

        $metadata = $client->readMetadata(
            new ReadMetadataRequest($filePath),
        );

        $this->assertEquals($pages, $metadata->getPages());
    }

    /**
     * @return list<list<int|string|OutputType>>
     */
    public static function providerReadingMetadata(): array
    {
        $provider = [
            [__DIR__.'/../files/pages-1.pdf', 1],
            [__DIR__.'/../files/pages-2.pdf', 2],
            [__DIR__.'/../files/pages-3.pdf', 3],
            [__DIR__.'/../files/pages-4.pdf', 4],
        ];

        return $provider;
    }

    public function testExtractingImageDataRequiresValidPdfToPpmBinary(): void
    {
        $pdfToPpmBinary = 'invalid_pdftoppm_binary';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "'.$pdfToPpmBinary.'" could not be found.');

        new PopplerExtractorClient(pdfToPpmBinary: $pdfToPpmBinary)->extractData(new ExtractDataRequest(__FILE__))->current();
    }

    public function testExtractingTextDataRequiresValidPdfToTextBinary(): void
    {
        $pdfToTextBinary = 'invalid_pdftotext_binary';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "'.$pdfToTextBinary.'" could not be found.');

        new PopplerExtractorClient(pdfToTextBinary: $pdfToTextBinary)->extractData(new ExtractTextRequest(__FILE__))->current();
    }

    public function testExtractingDataRequiresValidPdfFile(): void
    {
        $this->expectException(ExtractingDataFailedException::class);
        $this->expectExceptionMessageMatches('/May not be a PDF file/');

        new PopplerExtractorClient()->extractData(new ExtractDataRequest(__FILE__))->current();
    }

    public function testExtractingDataRequiresValidPage(): void
    {
        $page = random_int(2, 100);

        $this->expectException(ExtractingDataFailedException::class);
        $this->expectExceptionMessageMatches('/Wrong page range given/');

        new PopplerExtractorClient()->extractData(new ExtractDataRequest(__DIR__.'/../files/pages-1.pdf', $page, $page))->current();
    }

    #[DataProvider('providerExtractingDataRange')]
    public function testExtractingDataRange(
        string $filePath,
        int $firstPage,
        ?int $lastPage,
        int $responseCount,
    ): void {
        $client = new PopplerExtractorClient();

        $request = new ExtractDataRequest(
            $filePath, $firstPage, $lastPage,
        );

        /** @var list<ExtractedDataResponseInterface> $responses */
        $responses = iterator_to_array($client->extractData($request));

        $this->assertCount($responseCount, $responses);
        $this->assertContainsOnlyInstancesOf(ExtractedDataResponseInterface::class, $responses); // @phpstan-ignore-line
    }

    /**
     * @return list<list<int|string|null>>
     */
    public static function providerExtractingDataRange(): array
    {
        $provider = [
            [__DIR__.'/../files/pages-1.pdf', 1, 1, 1],
            [__DIR__.'/../files/pages-1.pdf', 1, null, 1],
            [__DIR__.'/../files/pages-2.pdf', 1, 1, 1],
            [__DIR__.'/../files/pages-2.pdf', 1, 2, 2],
            [__DIR__.'/../files/pages-2.pdf', 1, null, 2],
            [__DIR__.'/../files/pages-2.pdf', 2, 2, 1],
            [__DIR__.'/../files/pages-2.pdf', 2, null, 1],
            [__DIR__.'/../files/pages-3.pdf', 1, 1, 1],
            [__DIR__.'/../files/pages-3.pdf', 1, 2, 2],
            [__DIR__.'/../files/pages-3.pdf', 1, 3, 3],
            [__DIR__.'/../files/pages-3.pdf', 1, null, 3],
            [__DIR__.'/../files/pages-3.pdf', 2, 2, 1],
            [__DIR__.'/../files/pages-3.pdf', 2, 3, 2],
            [__DIR__.'/../files/pages-3.pdf', 2, null, 2],
            [__DIR__.'/../files/pages-3.pdf', 3, 3, 1],
            [__DIR__.'/../files/pages-3.pdf', 3, null, 1],
            [__DIR__.'/../files/pages-4.pdf', 1, 1, 1],
            [__DIR__.'/../files/pages-4.pdf', 1, 2, 2],
            [__DIR__.'/../files/pages-4.pdf', 1, 3, 3],
            [__DIR__.'/../files/pages-4.pdf', 1, 4, 4],
            [__DIR__.'/../files/pages-4.pdf', 1, null, 4],
            [__DIR__.'/../files/pages-4.pdf', 2, 2, 1],
            [__DIR__.'/../files/pages-4.pdf', 2, 3, 2],
            [__DIR__.'/../files/pages-4.pdf', 2, 4, 3],
            [__DIR__.'/../files/pages-4.pdf', 2, null, 3],
            [__DIR__.'/../files/pages-4.pdf', 3, 3, 1],
            [__DIR__.'/../files/pages-4.pdf', 3, 4, 2],
            [__DIR__.'/../files/pages-4.pdf', 3, null, 2],
            [__DIR__.'/../files/pages-4.pdf', 4, 4, 1],
            [__DIR__.'/../files/pages-4.pdf', 4, null, 1],
        ];

        return $provider;
    }

    #[DataProvider('providerExtractingTextData')]
    public function testExtractingTextData(
        string $filePath,
        int $page,
        string $text,
    ): void {
        $client = new PopplerExtractorClient();

        $request = new ExtractTextRequest(
            $filePath, $page, $page,
        );

        /** @var list<ExtractedDataResponseInterface> $responses */
        $responses = iterator_to_array($client->extractData($request));

        $this->assertCount(1, $responses);
        $this->assertEquals($page, $responses[0]->getPage());
        $this->assertStringContainsString($text, $responses[0]);
    }

    /**
     * @return list<list<int|string>>
     */
    public static function providerExtractingTextData(): array
    {
        $provider = [
            [__DIR__.'/../files/pages-1.pdf', 1, ''],
            [__DIR__.'/../files/pages-2.pdf', 1, 'Amazon Simple Storage Service'],
            [__DIR__.'/../files/pages-2.pdf', 2, 'Storage Service: API Reference'],
            [__DIR__.'/../files/pages-3.pdf', 3, 'Learn more about the AWS CLI'],
            [__DIR__.'/../files/pages-4.pdf', 4, 'API Version 2006-03-01 iv'],
        ];

        return $provider;
    }

    #[DataProvider('providerExtractingImageData')]
    public function testExtractingImageData(
        string $filePath,
        int $firstPage,
        OutputType $outputType,
        int $resolution,
        string $md5Hash,
    ): void {
        $client = new PopplerExtractorClient();

        $request = new ExtractDataRequest(
            $filePath,
            $firstPage,
            $firstPage,
            $outputType,
            $resolution,
        );

        /** @var list<ExtractedDataResponseInterface> $responses */
        $responses = iterator_to_array($client->extractData($request));

        $this->assertCount(1, $responses);
        $this->assertNotEmpty($responses[0]->getData());
        $this->assertEquals($md5Hash, md5($responses[0]));

        $image = imagecreatefromstring($responses[0]);
        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertGreaterThan(0, imagesx($image));
        $this->assertGreaterThan(0, imagesy($image));
    }

    /**
     * @return list<list<int|non-empty-string|OutputType>>
     */
    public static function providerExtractingImageData(): array
    {
        $provider = [
            [__DIR__.'/../files/pages-1.pdf', 1, OutputType::Jpeg, 48, '832bfdfd9a01a3087f765b54684347f4'],
            [__DIR__.'/../files/pages-1.pdf', 1, OutputType::Jpeg, 72, '8d56f696328dfaf06c963e1179456d25'],
            [__DIR__.'/../files/pages-1.pdf', 1, OutputType::Jpeg, 150, '080f873a6769b81d38f877d511e22a3c'],
            [__DIR__.'/../files/pages-1.pdf', 1, OutputType::Jpeg, 300, '7573ebf741870bb85c30196013397a55'],
            [__DIR__.'/../files/pages-1.pdf', 1, OutputType::Png, 48, 'a3b9529090369a93045cefe5b71151d6'],
            [__DIR__.'/../files/pages-1.pdf', 1, OutputType::Png, 300, '96a6476a69171db7a8ac55848323b219'],
            [__DIR__.'/../files/pages-2.pdf', 2, OutputType::Jpeg, 72, '3aa7339e59d5991590a14e26a2057002'],
            [__DIR__.'/../files/pages-3.pdf', 3, OutputType::Jpeg, 72, 'f06ac9888a4da750bb7450f3955e7123'],
            [__DIR__.'/../files/pages-4.pdf', 4, OutputType::Jpeg, 72, '48c209de64d027ed053a9e931da4ebc0'],
        ];

        return $provider;
    }
}
