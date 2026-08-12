<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\Pipeline;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Pipeline\Requests\PipelineListRequest;
use Manzadey\SaloonAmoCrm\Modules\Pipeline\Responses\PipelineListResponse;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

class PipelineListRequestTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $this->connector = new MainConnector(
            'test.amocrm.ru',
            static fn () => new AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        );
    }

    public function testSendReturnsPipelinesWithNestedStatuses(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make([
                '_embedded' => [
                    'pipelines' => [
                        [
                            'id' => 1,
                            'name' => 'Sales',
                            'sort' => 1,
                            'is_main' => true,
                            '_embedded' => [
                                'statuses' => [
                                    ['id' => 142, 'name' => 'Incoming lead', 'pipeline_id' => 1],
                                    ['id' => 143, 'name' => 'Won', 'pipeline_id' => 1],
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]));

        $response = $this->connector->send(new PipelineListRequest($this->connector));

        $this->assertInstanceOf(PipelineListResponse::class, $response);
        $this->assertTrue($response->isNotEmpty());

        $pipeline = $response->pipelines()[0];
        $this->assertSame(1, $pipeline->id());
        $this->assertSame('Sales', $pipeline->name());
        $this->assertTrue($pipeline->isMain());
        $this->assertCount(2, $pipeline->statuses());
        $this->assertSame(142, $pipeline->statuses()[0]->id());
        $this->assertSame('Incoming lead', $pipeline->statuses()[0]->name());
    }

    public function testEmptyAccountReturnsNoContentWithoutBody(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make(body: '', status: 204),
        ]));

        $response = $this->connector->send(new PipelineListRequest($this->connector));

        $this->assertSame([], $response->pipelines());
        $this->assertTrue($response->isEmpty());
    }
}
