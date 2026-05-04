<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Account\AccountWithQueryEnum;
use Manzadey\SaloonAmoCrm\Modules\Account\Requests\AccountRequest;
use Manzadey\SaloonAmoCrm\Modules\Account\Responses\AccountResponse;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;

class AccountRequestTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $auth = static fn() => new AccessTokenAuthenticator('access_token');
        $this->connector = new MainConnector('test.amocrm.ru', $auth);
    }

    public function testResolveEndpoint(): void
    {
        $request = new AccountRequest($this->connector);

        $this->assertSame('/account', $request->resolveEndpoint());
    }

    public function testResponseClass(): void
    {
        $request = new AccountRequest($this->connector);

        $reflection = new \ReflectionClass($request);
        $property = $reflection->getProperty('response');

        $this->assertSame(AccountResponse::class, $property->getValue($request));
    }

    public function testEmptyWithProducesNoQueryString(): void
    {
        $request = new AccountRequest($this->connector);

        $this->assertSame([], $this->invokeDefaultQuery($request));
    }

    public function testConstructorWithStringValuesIsPreserved(): void
    {
        $request = new AccountRequest($this->connector, ['amojo_id', 'version']);

        $this->assertSame(['with' => 'amojo_id,version'], $this->invokeDefaultQuery($request));
    }

    public function testConstructorWithEnumInstancesDoesNotCrash(): void
    {
        $request = new AccountRequest(
            $this->connector,
            [AccountWithQueryEnum::AMOJO_ID, AccountWithQueryEnum::VERSION]
        );

        $this->assertSame(['with' => 'amojo_id,version'], $this->invokeDefaultQuery($request));
    }

    public function testConstructorAcceptsMixedStringAndEnum(): void
    {
        $request = new AccountRequest(
            $this->connector,
            ['amojo_id', AccountWithQueryEnum::VERSION]
        );

        $this->assertSame(['with' => 'amojo_id,version'], $this->invokeDefaultQuery($request));
    }

    public function testWithEnumAddsValue(): void
    {
        $request = (new AccountRequest($this->connector))
            ->with(AccountWithQueryEnum::AMOJO_ID);

        $this->assertSame(['with' => 'amojo_id'], $this->invokeDefaultQuery($request));
    }

    public function testWithStringAddsValue(): void
    {
        $request = (new AccountRequest($this->connector))->with('amojo_id');

        $this->assertSame(['with' => 'amojo_id'], $this->invokeDefaultQuery($request));
    }

    public function testWithIsFluent(): void
    {
        $request = (new AccountRequest($this->connector))
            ->with(AccountWithQueryEnum::AMOJO_ID)
            ->with(AccountWithQueryEnum::VERSION);

        $this->assertSame(['with' => 'amojo_id,version'], $this->invokeDefaultQuery($request));
    }

    public function testWithDoesNotDuplicate(): void
    {
        $request = (new AccountRequest($this->connector))
            ->with(AccountWithQueryEnum::AMOJO_ID)
            ->with(AccountWithQueryEnum::AMOJO_ID)
            ->with('amojo_id');

        $this->assertSame(['with' => 'amojo_id'], $this->invokeDefaultQuery($request));
    }

    public function testWithAddedAfterConstructorIsAppended(): void
    {
        $request = new AccountRequest($this->connector, [AccountWithQueryEnum::AMOJO_ID]);
        $request->with(AccountWithQueryEnum::VERSION);

        $this->assertSame(['with' => 'amojo_id,version'], $this->invokeDefaultQuery($request));
    }

    public function testWithAllReplacesWithFullEnumList(): void
    {
        $request = (new AccountRequest($this->connector, ['amojo_id']))->withAll();

        $expected = implode(',', array_map(
            static fn(AccountWithQueryEnum $c): string => $c->value,
            AccountWithQueryEnum::cases()
        ));

        $this->assertSame(['with' => $expected], $this->invokeDefaultQuery($request));
    }

    public function testSendThroughMockClientReturnsTypedResponse(): void
    {
        $mockClient = new MockClient([
            AccountRequest::class => MockResponse::make(['id' => 1, 'name' => 'Acme'], 200),
        ]);

        $this->connector->withMockClient($mockClient);

        $response = (new AccountRequest($this->connector))
            ->with(AccountWithQueryEnum::AMOJO_ID)
            ->send();

        $this->assertInstanceOf(AccountResponse::class, $response);
        $mockClient->assertSent(static fn(Request $request): bool => $request instanceof AccountRequest);
        $mockClient->assertSentCount(1);
    }

    /**
     * @return array<string, mixed>
     */
    private function invokeDefaultQuery(AccountRequest $request): array
    {
        $reflection = new \ReflectionMethod($request, 'defaultQuery');

        return $reflection->invoke($request);
    }
}
