<?php

namespace OneToMany\PdfPack\Tests\Client\Poppler;

use OneToMany\PdfPack\Client\Exception\ConvertingPdfFailedException;
use OneToMany\PdfPack\Client\Exception\ReadingPdfFailedException;
use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Exception\InvalidArgumentException;
use OneToMany\PdfPack\Request\ConvertPdfRequest;
use OneToMany\PdfPack\Request\ReadPdfRequest;
use OneToMany\PdfPack\Response\ConvertPdfResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function iterator_to_array;
use function md5;

#[Large]
#[Group('UnitTests')]
#[Group('ClientTests')]
#[Group('PopplerTests')]
final class PopplerClientTest extends TestCase
{
    public function testReadingFileRequiresValidPdfInfoBinary(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "invalid_pdfinfo_binary" could not be found.');

        new PopplerClient(pdfInfoBinary: 'invalid_pdfinfo_binary')->read(new ReadPdfRequest(__FILE__));
    }

    public function testReadingFileRequiresValidPdfFile(): void
    {
        $this->expectException(ReadingPdfFailedException::class);
        $this->expectExceptionMessageMatches('/May not be a PDF file/');

        new PopplerClient()->read(new ReadPdfRequest(__FILE__));
    }

    #[DataProvider('providerPathAndPages')]
    public function testReadingFile(string $path, int $pages): void
    {
        $response = new PopplerClient()->read(...[
            'request' => new ReadPdfRequest($path),
        ]);

        $this->assertEquals($pages, $response->getPages());
    }

    /**
     * @return list<list<int|non-empty-string|OutputType>>
     */
    public static function providerPathAndPages(): array
    {
        $provider = [
            [__DIR__.'/../../.data/pages-1.pdf', 1],
            [__DIR__.'/../../.data/pages-2.pdf', 2],
            [__DIR__.'/../../.data/pages-3.pdf', 3],
            [__DIR__.'/../../.data/pages-4.pdf', 4],
        ];

        return $provider;
    }

    public function testConvertingPdfToImageRequiresValidPdfToPpmBinary(): void
    {
        $request = new ConvertPdfRequest(__DIR__.'/../../.data/pages-1.pdf')->asJpegOutput();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "invalid_pdftoppm_binary" could not be found.');

        new PopplerClient(pdfToPpmBinary: 'invalid_pdftoppm_binary')->convert($request)->current();
    }

    public function testConvertingPdfToTextDataRequiresValidPdfToTextBinary(): void
    {
        $request = new ConvertPdfRequest(__DIR__.'/../../.data/pages-1.pdf')->asTextOutput();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "invalid_pdftotext_binary" could not be found.');

        new PopplerClient(pdfToTextBinary: 'invalid_pdftotext_binary')->convert($request)->current();
    }

    public function testConvertingPdfRequiresValidPdfFile(): void
    {
        $request = new ConvertPdfRequest(__FILE__)->toPage(1);

        $this->expectException(ConvertingPdfFailedException::class);
        $this->expectExceptionMessageMatches('/May not be a PDF file/');

        new PopplerClient()->convert($request)->current();
    }

    public function testConvertingPdfRequiresValidPage(): void
    {
        $request = new ConvertPdfRequest(__DIR__.'/../../.data/pages-1.pdf')->fromPage(2)->toPage(2);

        $this->expectException(ConvertingPdfFailedException::class);
        $this->expectExceptionMessageMatches('/Wrong page range given/');

        new PopplerClient()->convert($request)->current();
    }

    #[DataProvider('providerPathFirstPageLastPageAndResponseCount')]
    public function testConvertingOneOrMorePdfPagesToEqualNumberOfImages(
        string $path,
        int $firstPage,
        ?int $lastPage,
        int $responseCount,
    ): void {
        $request = new ConvertPdfRequest($path, $firstPage, $lastPage);

        /** @var list<ConvertPdfResponse> $responses */
        $responses = iterator_to_array(new PopplerClient()->convert($request));

        $this->assertCount($responseCount, $responses);
        $this->assertContainsOnlyInstancesOf(ConvertPdfResponse::class, $responses);
    }

    /**
     * @return list<list<int|string|null>>
     */
    public static function providerPathFirstPageLastPageAndResponseCount(): array
    {
        $provider = [
            [__DIR__.'/../../.data/pages-1.pdf', 1, 1, 1],
            [__DIR__.'/../../.data/pages-1.pdf', 1, null, 1],
            [__DIR__.'/../../.data/pages-2.pdf', 1, 1, 1],
            [__DIR__.'/../../.data/pages-2.pdf', 1, 2, 2],
            [__DIR__.'/../../.data/pages-2.pdf', 1, null, 2],
            [__DIR__.'/../../.data/pages-2.pdf', 2, 2, 1],
            [__DIR__.'/../../.data/pages-2.pdf', 2, null, 1],
            [__DIR__.'/../../.data/pages-3.pdf', 1, 1, 1],
            [__DIR__.'/../../.data/pages-3.pdf', 1, 2, 2],
            [__DIR__.'/../../.data/pages-3.pdf', 1, 3, 3],
            [__DIR__.'/../../.data/pages-3.pdf', 1, null, 3],
            [__DIR__.'/../../.data/pages-3.pdf', 2, 2, 1],
            [__DIR__.'/../../.data/pages-3.pdf', 2, 3, 2],
            [__DIR__.'/../../.data/pages-3.pdf', 2, null, 2],
            [__DIR__.'/../../.data/pages-3.pdf', 3, 3, 1],
            [__DIR__.'/../../.data/pages-3.pdf', 3, null, 1],
            [__DIR__.'/../../.data/pages-4.pdf', 1, 1, 1],
            [__DIR__.'/../../.data/pages-4.pdf', 1, 2, 2],
            [__DIR__.'/../../.data/pages-4.pdf', 1, 3, 3],
            [__DIR__.'/../../.data/pages-4.pdf', 1, 4, 4],
            [__DIR__.'/../../.data/pages-4.pdf', 1, null, 4],
            [__DIR__.'/../../.data/pages-4.pdf', 2, 2, 1],
            [__DIR__.'/../../.data/pages-4.pdf', 2, 3, 2],
            [__DIR__.'/../../.data/pages-4.pdf', 2, 4, 3],
            [__DIR__.'/../../.data/pages-4.pdf', 2, null, 3],
            [__DIR__.'/../../.data/pages-4.pdf', 3, 3, 1],
            [__DIR__.'/../../.data/pages-4.pdf', 3, 4, 2],
            [__DIR__.'/../../.data/pages-4.pdf', 3, null, 2],
            [__DIR__.'/../../.data/pages-4.pdf', 4, 4, 1],
            [__DIR__.'/../../.data/pages-4.pdf', 4, null, 1],
        ];

        return $provider;
    }

    #[DataProvider('providerPathPageAndText')]
    public function testConvertingPdfToText(string $path, int $page, string $text): void
    {
        $request = new ConvertPdfRequest($path, $page, $page)->asTextOutput();

        /** @var list<ConvertPdfResponse> $responses */
        $responses = iterator_to_array(new PopplerClient()->convert($request));

        $this->assertCount(1, $responses);
        $this->assertEquals($page, $responses[0]->getPage());
        $this->assertStringContainsString($text, $responses[0]);
    }

    /**
     * @return list<list<int|string>>
     */
    public static function providerPathPageAndText(): array
    {
        $provider = [
            [__DIR__.'/../../.data/pages-1.pdf', 1, ''],
            [__DIR__.'/../../.data/pages-2.pdf', 1, 'Amazon Simple Storage Service'],
            [__DIR__.'/../../.data/pages-2.pdf', 2, 'Storage Service: API Reference'],
            [__DIR__.'/../../.data/pages-3.pdf', 3, 'Learn more about the AWS CLI'],
            [__DIR__.'/../../.data/pages-4.pdf', 4, 'API Version 2006-03-01 iv'],
        ];

        return $provider;
    }

    #[DataProvider('providerPathFirstPageLastPageOutputTypeResolutionAndMd5Hash')]
    public function testConvertingPdfToImage(string $path, int $firstPage, OutputType $outputType, int $resolution, string $md5Hash): void
    {
        $request = new ConvertPdfRequest($path, $firstPage, $firstPage, $outputType, $resolution);

        /** @var list<ConvertPdfResponse> $responses */
        $responses = iterator_to_array(new PopplerClient()->convert($request));

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
    public static function providerPathFirstPageLastPageOutputTypeResolutionAndMd5Hash(): array
    {
        $provider = [
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 48, '832bfdfd9a01a3087f765b54684347f4'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 72, '8d56f696328dfaf06c963e1179456d25'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 150, '080f873a6769b81d38f877d511e22a3c'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 300, '7573ebf741870bb85c30196013397a55'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Png, 48, 'a3b9529090369a93045cefe5b71151d6'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Png, 300, '96a6476a69171db7a8ac55848323b219'],
            [__DIR__.'/../../.data/pages-2.pdf', 2, OutputType::Jpeg, 72, '3aa7339e59d5991590a14e26a2057002'],
            [__DIR__.'/../../.data/pages-3.pdf', 3, OutputType::Jpeg, 72, 'f06ac9888a4da750bb7450f3955e7123'],
            [__DIR__.'/../../.data/pages-4.pdf', 4, OutputType::Jpeg, 72, '48c209de64d027ed053a9e931da4ebc0'],
        ];

        return $provider;
    }
}
