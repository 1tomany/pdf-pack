<?php

namespace OneToMany\PdfPack\Tests\Action;

use OneToMany\PdfPack\Action\ConvertAction;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Contract\Enum\OutputType;
use OneToMany\PdfPack\Transfer\Record\PageRecord;
use OneToMany\PdfPack\Transfer\Request\ConvertRequest;
use OneToMany\PdfPack\Transfer\Response\ConvertResponse;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function iterator_to_array;

#[Group('UnitTests')]
#[Group('ActionTests')]
final class ConvertActionTest extends TestCase
{
    public function testActLazilyWrapsPageRecordsInConvertResponses(): void
    {
        $records = [
            3 => new PageRecord(OutputType::Jpeg, 'Page 1', 1),
            7 => new PageRecord(OutputType::Jpeg, 'Page 2', 2),
        ];

        $yieldedRecordCount = 0;
        $recordGenerator = (static function () use ($records, &$yieldedRecordCount): \Generator {
            foreach ($records as $key => $record) {
                ++$yieldedRecordCount;

                yield $key => $record;
            }
        })();

        $client = $this->createStub(ClientInterface::class);
        $client->method('convert')->willReturn($recordGenerator);

        $responseGenerator = new ConvertAction($client)->act(new ConvertRequest(__FILE__));

        $this->assertSame(0, $yieldedRecordCount);

        /** @var array<int, ConvertResponse> $responses */
        $responses = iterator_to_array($responseGenerator);

        $this->assertSame(2, $yieldedRecordCount);
        $this->assertSame(array_keys($records), array_keys($responses));

        foreach ($responses as $key => $response) {
            $this->assertSame($records[$key], $response->getRecord());
        }
    }
}
