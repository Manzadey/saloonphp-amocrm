<?php

declare(strict_types=1);

namespace Manzadey\tests\Modules\Tag;

use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagCreateRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Requests\TagListRequest;
use Manzadey\SaloonAmoCrm\Modules\Tag\Responses\TagCreateResponse;
use Manzadey\SaloonAmoCrm\Modules\Tag\Responses\TagListResponse;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

class TagRequestsTest extends TestCase
{
    private MainConnector $connector;

    protected function setUp(): void
    {
        $this->connector = new MainConnector(
            'test.amocrm.ru',
            static fn () => new AccessTokenAuthenticator('token', 'refresh', new DateTimeImmutable()),
        );
    }

    public function testListReturnsTypedTags(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make([
                '_page' => 1,
                '_embedded' => [
                    'tags' => [
                        ['id' => 263807, 'name' => 'Tag 1', 'color' => 'DDEBB5'],
                        ['id' => 263809, 'name' => 'Tag 2', 'color' => null],
                    ],
                ],
            ]),
        ]));

        $response = (new TagListRequest($this->connector, 'leads'))->send();

        self::assertInstanceOf(TagListResponse::class, $response);
        self::assertSame(1, $response->page());
        self::assertCount(2, $response->tags());
        self::assertSame('Tag 1', $response->tags()->first()?->name());
        self::assertSame('DDEBB5', $response->tags()->first()?->color());
    }

    public function testCreateReturnsTheCreatedTags(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make([
                '_embedded' => [
                    'tags' => [
                        ['id' => 263807, 'name' => 'Tag 1', 'request_id' => '0'],
                    ],
                ],
            ]),
        ]));

        $response = TagCreateRequest::make($this->connector, 'leads')
            ->add(TagModel::make()->setName('Tag 1'))
            ->send();

        self::assertInstanceOf(TagCreateResponse::class, $response);
        self::assertSame(263807, $response->tags()->first()?->id());
    }

    public function testEmptyBodyYieldsEmptyCollection(): void
    {
        $this->connector->withMockClient(new MockClient([
            MockResponse::make([], 204),
        ]));

        self::assertTrue((new TagListRequest($this->connector, 'leads'))->send()->tags()->isEmpty());
    }
}
