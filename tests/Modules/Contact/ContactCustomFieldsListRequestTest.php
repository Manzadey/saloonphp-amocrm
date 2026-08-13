<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\Contact;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Contact\Requests\ContactCustomFieldsListRequest;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Responses\CustomFieldsListResponse;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

class ContactCustomFieldsListRequestTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $this->connector = new MainConnector(
            'test.amocrm.ru',
            static fn () => new AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        );
    }

    public function testSendReturnsTypedResponseWithPagination(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make([
                '_embedded' => [
                    'custom_fields' => [
                        ['id' => 1, 'name' => 'Phone'],
                    ],
                ],
            ]),
        ]));

        $response = $this->connector->send(
            (new ContactCustomFieldsListRequest($this->connector))->page(1)->limit(50)
        );

        $this->assertInstanceOf(CustomFieldsListResponse::class, $response);
        $this->assertCount(1, $response->fields());
    }

    public function testLimitAndPageAreSentAsQueryParameters(): void
    {
        $mockClient = new MockClient([
            MockResponse::make(['_embedded' => ['custom_fields' => []]]),
        ]);

        $this->connector->withMockClient($mockClient);

        $this->connector->send((new ContactCustomFieldsListRequest($this->connector))->page(2)->limit(25));

        $mockClient->assertSent(static function (ContactCustomFieldsListRequest $request): bool {
            return $request->query()->get('page') === 2
                && $request->query()->get('limit') === 25;
        });
    }
}
