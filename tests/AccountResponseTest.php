<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Account\Requests\AccountRequest;
use Manzadey\SaloonAmoCrm\Modules\Account\Responses\AccountResponse;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

class AccountResponseTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $auth = static fn() => new AccessTokenAuthenticator('access_token');
        $this->connector = new MainConnector('test.amocrm.ru', $auth);
    }

    /**
     * @param array<string, mixed> $body
     */
    private function makeResponse(array $body): AccountResponse
    {
        $mockClient = new MockClient([
            AccountRequest::class => MockResponse::make($body, 200),
        ]);

        $this->connector->withMockClient($mockClient);

        $response = (new AccountRequest($this->connector))->send();

        $this->assertInstanceOf(AccountResponse::class, $response);

        return $response;
    }

    public function testGettersReturnValuesFromJson(): void
    {
        $response = $this->makeResponse([
            'id' => 1234,
            'name' => 'Acme Corp',
            'subdomain' => 'acme',
            'created_at' => 1_700_000_000,
            'created_by' => 1,
            'updated_at' => 1_700_000_500,
            'updated_by' => 2,
            'current_user_id' => 3,
            'country' => 'RU',
            'currency' => 'RUB',
            'currency_symbol' => '₽',
        ]);

        $this->assertSame(1234, $response->getId());
        $this->assertSame('Acme Corp', $response->getName());
        $this->assertSame('acme', $response->getSubdomain());
        $this->assertSame(1_700_000_000, $response->getCreatedAt());
        $this->assertSame(1, $response->getCreatedBy());
        $this->assertSame(1_700_000_500, $response->getUpdatedAt());
        $this->assertSame(2, $response->getUpdatedBy());
        $this->assertSame(3, $response->getCurrentUserId());
        $this->assertSame('RU', $response->getCountry());
        $this->assertSame('RUB', $response->getCurrency());
        $this->assertSame('₽', $response->getCurrencySymbol());
    }

    public function testGettersReturnNullWhenFieldMissing(): void
    {
        $response = $this->makeResponse([
            'id' => 1234,
            'name' => 'Acme',
        ]);

        $this->assertSame(1234, $response->getId());
        $this->assertSame('Acme', $response->getName());
        $this->assertNull($response->getSubdomain());
        $this->assertNull($response->getCreatedAt());
        $this->assertNull($response->getCreatedBy());
        $this->assertNull($response->getUpdatedAt());
        $this->assertNull($response->getUpdatedBy());
        $this->assertNull($response->getCurrentUserId());
        $this->assertNull($response->getCountry());
        $this->assertNull($response->getCurrency());
        $this->assertNull($response->getCurrencySymbol());
    }

    public function testEmptyBodyReturnsAllNulls(): void
    {
        $response = $this->makeResponse([]);

        $this->assertNull($response->getId());
        $this->assertNull($response->getName());
        $this->assertNull($response->getSubdomain());
    }
}
