<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Query\HasFilterQuery;
use PHPUnit\Framework\TestCase;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class HasFilterQueryTest extends TestCase
{
    private function request(): Request
    {
        return new class () extends Request {
            use HasFilterQuery;

            protected Method $method = Method::GET;

            public function resolveEndpoint(): string
            {
                return '/';
            }
        };
    }

    public function testRepeatedScalarFilterOverwrites(): void
    {
        $request = $this->request()
            ->filter('entity_type', 'leads')
            ->filter('entity_type', 'contacts');

        self::assertSame(['entity_type' => 'contacts'], $request->query()->get('filter'));
    }

    public function testDistinctFilterKeysCoexist(): void
    {
        $request = $this->request()
            ->filter('id', 1)
            ->filter('name', 'John');

        self::assertSame(['id' => 1, 'name' => 'John'], $request->query()->get('filter'));
    }
}
