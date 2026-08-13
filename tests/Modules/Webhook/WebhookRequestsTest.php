<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\Webhook;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Requests\WebhookListRequest;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Requests\WebhookSubscribeRequest;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Requests\WebhookUnsubscribeRequest;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Responses\WebhookListResponse;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Responses\WebhookResponse;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Responses\WebhookUnsubscribeResponse;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

class WebhookRequestsTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $this->connector = new MainConnector(
            'test.amocrm.ru',
            static fn () => new AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        );
    }

    public function testListReturnsTypedWebhooks(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make([
                '_total_items' => 2,
                '_embedded' => [
                    'webhooks' => [
                        [
                            'id' => 839656,
                            'destination' => 'https://webhook-uri.com',
                            'created_at' => 1575539157,
                            'updated_at' => 1575539157,
                            'account_id' => 321321,
                            'created_by' => 123123,
                            'sort' => 1,
                            'disabled' => false,
                            'settings' => ['add_task'],
                        ],
                        [
                            'id' => 849193,
                            'destination' => 'https://api.test.ru/amoWebHook',
                            'created_at' => 1576157524,
                            'updated_at' => 1585816857,
                            'account_id' => 321321,
                            'created_by' => 123123,
                            'sort' => 2,
                            'disabled' => true,
                            'settings' => ['update_lead'],
                        ],
                    ],
                ],
            ]),
        ]));

        $response = $this->connector->send(new WebhookListRequest($this->connector));

        $this->assertInstanceOf(WebhookListResponse::class, $response);
        $this->assertTrue($response->webhooks()->isNotEmpty());
        $this->assertCount(2, $response->webhooks());

        $webhook = $response->webhooks()->first();
        $this->assertSame(839656, $webhook->id());
        $this->assertSame('https://webhook-uri.com', $webhook->destination());
        $this->assertSame(['add_task'], $webhook->settings());
        $this->assertSame(1, $webhook->sort());
        $this->assertSame(321321, $webhook->accountId());
        $this->assertSame(123123, $webhook->createdBy());
        $this->assertSame(1575539157, $webhook->createdAt());
        $this->assertSame(1575539157, $webhook->updatedAt());
        $this->assertFalse($webhook->isDisabled());

        $disabled = $response->webhooks()->all()[1];
        $this->assertSame(2, $disabled->sort());
        $this->assertSame(1585816857, $disabled->updatedAt());
        $this->assertTrue($disabled->isDisabled());
    }

    public function testListWithoutSubscriptionsReturnsNoContentWithoutBody(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make(body: '', status: 204),
        ]));

        $response = $this->connector->send(new WebhookListRequest($this->connector));

        $this->assertSame([], $response->webhooks()->all());
        $this->assertTrue($response->webhooks()->isEmpty());
    }

    public function testSubscribeSendsDestinationAndSettingsInJsonBody(): void
    {
        $mockClient = new MockClient([
            MockResponse::make([
                'id' => 1,
                'destination' => 'https://example.com/webhook',
                'settings' => ['add_lead'],
            ]),
        ]);

        $this->connector->withMockClient($mockClient);

        $response = $this->connector->send(
            new WebhookSubscribeRequest($this->connector, 'https://example.com/webhook', ['add_lead'])
        );

        $this->assertInstanceOf(WebhookResponse::class, $response);
        $this->assertSame('https://example.com/webhook', $response->webhook()?->destination());

        $mockClient->assertSent(static function (WebhookSubscribeRequest $request): bool {
            return $request->body()->all() === [
                'destination' => 'https://example.com/webhook',
                'settings' => ['add_lead'],
            ];
        });
    }

    public function testUnsubscribeSendsDestinationInJsonBody(): void
    {
        $mockClient = new MockClient([
            MockResponse::make(body: '', status: 204),
        ]);

        $this->connector->withMockClient($mockClient);

        $response = $this->connector->send(
            new WebhookUnsubscribeRequest($this->connector, 'https://example.com/webhook')
        );

        $this->assertInstanceOf(WebhookUnsubscribeResponse::class, $response);

        $mockClient->assertSent(static function (WebhookUnsubscribeRequest $request): bool {
            return $request->body()->all() === ['destination' => 'https://example.com/webhook'];
        });
    }
}
