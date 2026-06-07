<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Filters\AbstractFilter;
use PHPUnit\Framework\TestCase;

class AbstractFilterTest extends TestCase
{
    private function filter(): AbstractFilter
    {
        return new class () extends AbstractFilter {
        };
    }

    public function testUpdatedAtUsesUpdatedAtKey(): void
    {
        $filter = $this->filter()->updatedAt(100, 200);

        self::assertSame(['from' => 100, 'to' => 200], $filter->get('updated_at'));
        self::assertNull($filter->get('updated'));
    }
}
