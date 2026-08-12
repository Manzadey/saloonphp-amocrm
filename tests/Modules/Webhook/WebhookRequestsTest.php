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
                '_embedded' => [
                    'webhooks' => [
                        [
                            'id' => 1,
                            'destination' => 'https://example.com/webhook',
                            'settings' => ['add_lead', 'update_lead'],
                            'disabled' => false,
                        ],
                    ],
                ],
            ]),
        ]));

        $response = $this->connector->send(new WebhookListRequest($this->connector));

        $this->assertInstanceOf(WebhookListResponse::class, $response);
        $this->assertTrue($response->isNotEmpty());

        $webhook = $response->webhooks()[0];
        $this->assertSame('https://example.com/webhook', $webhook->destination());
        $this->assertSame(['add_lead', 'update_lead'], $webhook->settings());
        $this->assertFalse($webhook->isDisabled());
    }

    public function testListWithoutSubscriptionsReturnsNoContentWithoutBody(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make(body: '', status: 204),
        ]));

        $response = $this->connector->send(new WebhookListRequest($this->connector));

        $this->assertSame([], $response->webhooks());
        $this->assertTrue($response->isEmpty());
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
