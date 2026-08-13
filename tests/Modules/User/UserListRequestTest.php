<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\User;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\User\Requests\UserItemRequest;
use Manzadey\SaloonAmoCrm\Modules\User\Requests\UserListRequest;
use Manzadey\SaloonAmoCrm\Modules\User\Responses\UserListResponse;
use Manzadey\SaloonAmoCrm\Modules\User\UserWith;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

class UserListRequestTest extends TestCase
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
                '_page' => 2,
                '_embedded' => [
                    'users' => [
                        ['id' => 1, 'name' => 'Ivan Ivanov'],
                        ['id' => 2, 'name' => 'Petr Petrov'],
                    ],
                ],
            ]),
        ]));

        $response = $this->connector->send((new UserListRequest($this->connector))->page(2)->limit(50));

        $this->assertInstanceOf(UserListResponse::class, $response);
        $this->assertSame(2, $response->page());
        $this->assertCount(2, $response->users());
        $this->assertSame('Ivan Ivanov', $response->users()[0]->name());
        $this->assertTrue($response->isNotEmpty());
    }

    public function testLimitAndPageAreSentAsQueryParameters(): void
    {
        $mockClient = new MockClient([
            MockResponse::make(['_embedded' => ['users' => []]]),
        ]);

        $this->connector->withMockClient($mockClient);

        $this->connector->send((new UserListRequest($this->connector))->page(3)->limit(10));

        $mockClient->assertSent(static function (UserListRequest $request): bool {
            return $request->query()->get('page') === 3
                && $request->query()->get('limit') === 10;
        });
    }

    public function testNamedWithHelpersAreJoinedIntoCsv(): void
    {
        $request = (new UserListRequest($this->connector))
            ->withRole()
            ->withGroup()
            ->withUserRank();

        $this->assertSame('role,group,user_rank', $request->query()->get('with'));
    }

    public function testWithAcceptsArrayOfValues(): void
    {
        $request = (new UserListRequest($this->connector))
            ->with([UserWith::UUID, UserWith::AMOJO_ID, UserWith::PHONE_NUMBER]);

        $this->assertSame('uuid,amojo_id,phone_number', $request->query()->get('with'));
    }

    public function testItemRequestSharesTheSameWithQuery(): void
    {
        $request = (new UserItemRequest($this->connector, 1))->with([UserWith::ROLE])->withGroup();

        $this->assertSame('role,group', $request->query()->get('with'));
    }
}
