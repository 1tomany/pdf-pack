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

        /** @var non-empty-list<ConvertPdfResponse> $responses */
        $responses = iterator_to_array(new PopplerClient()->convert($request));

        $this->assertCount($responseCount, $responses);
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
    public function testConvertingPdfToText(
        string $path,
        int $page,
        string $text,
    ): void {
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

    #[DataProvider('providerPathFirstPageLastPageOutputTypeResolutionAndHash')]
    public function testConvertingPdfToImage(
        string $path,
        int $firstPage,
        OutputType $outputType,
        int $resolution,
        string $hash,
    ): void {
        $request = new ConvertPdfRequest($path, $firstPage, $firstPage, $outputType, $resolution);

        /** @var list<ConvertPdfResponse> $responses */
        $responses = iterator_to_array(new PopplerClient()->convert($request));

        $this->assertCount(1, $responses);
        $this->assertNotEmpty($responses[0]->getData());
        $this->assertEquals($hash, $responses[0]->getHash());

        $image = imagecreatefromstring($responses[0]);
        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertGreaterThan(0, imagesx($image));
        $this->assertGreaterThan(0, imagesy($image));
    }

    /**
     * @return list<list<int|non-empty-string|OutputType>>
     */
    public static function providerPathFirstPageLastPageOutputTypeResolutionAndHash(): array
    {
        $provider = [
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 48, '28933b0d9af70074616a15d721f34c3f5d3bb6bb80f07a6992b572d8c3e80697'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 72, '1186d43f1e28e87ae6d380978ed1d5e5eacb19a67d8e478a1d610e9c24d06238'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 150, '1cb6e6f67bcffc8e16861c4789b67e06d4a59465933b98317183cff624a76df9'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Jpeg, 300, '7bf4599051ab17eca303d6264739916b8a6a51231c11d6041bd81bbe41661373'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Png, 48, 'afc38b7871f520934182b3ebf17f7bc3ef12ea0c87cb65416d3b540edf385083'],
            [__DIR__.'/../../.data/pages-1.pdf', 1, OutputType::Png, 300, 'beba09056cf8eb7f1359bc8fc1b486c35e0bffa40b216524fe61ffd176658c0b'],
            [__DIR__.'/../../.data/pages-2.pdf', 2, OutputType::Jpeg, 72, 'e1c2a91afd9508022090e1a4e836a63e48119aed48dd7bcf9e9cd1a857aaa90f'],
            [__DIR__.'/../../.data/pages-3.pdf', 3, OutputType::Jpeg, 72, '2ce164dc8d4b55df22b2faa94c88bea5caa4c5c6fd8998eb0d985aa1c51659a1'],
            [__DIR__.'/../../.data/pages-4.pdf', 4, OutputType::Jpeg, 72, 'f7553db6b12ad98c4d790788bc11823cd2a27f4bae35b227566b7927d177330f'],
        ];

        return $provider;
    }
}
