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

    public function testNewestOrdersByMostRecentFirst(): void
    {
        $request = $this->request()->newest();

        self::assertSame(['id' => 'desc'], $request->query()->get('order'));
    }

    public function testLatestOrdersByMostRecentFirst(): void
    {
        $request = $this->request()->latest(QueryOrderFieldEnum::CREATED_AT);

        self::assertSame(['created_at' => 'desc'], $request->query()->get('order'));
    }
}
