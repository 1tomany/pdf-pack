<?php

namespace OneToMany\PdfPack\Tests;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Contract\Enum\Vendor;
use OneToMany\PdfPack\Factory\ClientContainer;
use OneToMany\PdfPack\Factory\ClientFactory;
use OneToMany\PdfPack\PdfClient;
use OneToMany\PdfPack\Transfer\Record\PageRecord;
use OneToMany\PdfPack\Transfer\Record\PdfRecord;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Request\ReadRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function array_values;

#[Group('UnitTests')]
final class PdfClientTest extends TestCase
{
    public function testUsingClientReturnsScopedFacadeWithoutChangingDefault(): void
    {
        $poppler = new PopplerTestClient();
        $imagick = new ImagickTestClient();
        $client = $this->createPdfClient($poppler, $imagick);

        $this->assertSame(1, $client->files->read(__FILE__)->getPageCount());
        $this->assertSame(2, $client->use(' IMAGICK ')->files->read(__FILE__)->getPageCount());
        $this->assertSame(1, $client->files->read(__FILE__)->getPageCount());
    }

    public function testReadIsAvailableAsConvenienceAlias(): void
    {
        $client = $this->createPdfClient(new PopplerTestClient());

        $this->assertEquals($client->files->read(__FILE__), $client->read(__FILE__));
    }

    public function testConvertReturnsGeneratorAndConvertsPagesOnDemand(): void
    {
        $poppler = new PopplerTestClient();
        $client = $this->createPdfClient($poppler);

        $pages = $client->files->convert(__FILE__, 2, 3, OutputType::Png, 150);

        $this->assertInstanceOf(\Generator::class, $pages);
        $this->assertSame(0, $poppler->convertCalls);

        $page = $pages->current();
        $request = $poppler->getConvertRequest();

        $this->assertSame(1, $poppler->convertCalls);
        $this->assertInstanceOf(PageRecord::class, $page);
        $this->assertSame(2, $request->getFirstPage());
        $this->assertSame(3, $request->getLastPage());
        $this->assertSame(OutputType::Png, $request->getOutputType());
        $this->assertSame(150, $request->getResolution());
    }

    public function testConvertIsAvailableAsConvenienceAlias(): void
    {
        $client = $this->createPdfClient(new PopplerTestClient());

        $this->assertInstanceOf(PageRecord::class, $client->convert(__FILE__)->current());
    }

    private function createPdfClient(ClientInterface ...$clients): PdfClient
    {
        return new PdfClient(new ClientFactory(new ClientContainer(array_values($clients))));
    }
}

class PopplerTestClient implements ClientInterface
{
    public int $convertCalls = 0;

    public ?ConvertRequest $convertRequest = null;

    #[\Override]
    public static function getVendor(): string|Vendor
    {
        return Vendor::Poppler;
    }

    #[\Override]
    public function read(ReadRequest $request): PdfRecord
    {
        return new PdfRecord($request->getPath(), 1);
    }

    #[\Override]
    public function convert(ConvertRequest $request): \Generator
    {
        ++$this->convertCalls;
        $this->convertRequest = $request;

        yield new PageRecord($request->getOutputType(), 'page', $request->getFirstPage());
    }

    public function getConvertRequest(): ConvertRequest
    {
        return $this->convertRequest ?? throw new \LogicException('No conversion was requested.');
    }
}

final class ImagickTestClient extends PopplerTestClient
{
    #[\Override]
    public static function getVendor(): string
    {
        return 'imagick';
    }

    #[\Override]
    public function read(ReadRequest $request): PdfRecord
    {
        return new PdfRecord($request->getPath(), 2);
    }
}
