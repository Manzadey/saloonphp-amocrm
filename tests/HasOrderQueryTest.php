<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderFieldEnum;
use Manzadey\SaloonAmoCrm\Query\HasOrderQuery;
use PHPUnit\Framework\TestCase;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class HasOrderQueryTest extends TestCase
{
    private function request(): Request
    {
        return new class () extends Request {
            use HasOrderQuery;

            protected Method $method = Method::GET;

            public function resolveEndpoint(): string
            {
                return '/';
            }
        };
    }

    public function testLatestOrdersByMostRecentFirst(): void
    {
        $request = $this->request()->latest();

        self::assertSame(['id' => 'desc'], $request->query()->get('order'));
    }

    public function testLatestAcceptsExplicitField(): void
    {
        $request = $this->request()->latest(QueryOrderFieldEnum::CREATED_AT);

        self::assertSame(['created_at' => 'desc'], $request->query()->get('order'));
    }

    public function testOldestOrdersByLeastRecentFirst(): void
    {
        $request = $this->request()->oldest();

        self::assertSame(['id' => 'asc'], $request->query()->get('order'));
    }

    public function testOldestAcceptsExplicitField(): void
    {
        $request = $this->request()->oldest(QueryOrderFieldEnum::UPDATED_AT);

        self::assertSame(['updated_at' => 'asc'], $request->query()->get('order'));
    }
}
