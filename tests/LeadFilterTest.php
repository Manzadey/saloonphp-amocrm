<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadFilter;
use PHPUnit\Framework\TestCase;

class LeadFilterTest extends TestCase
{
    public function testKeysMovedOutOfTheBaseStillWork(): void
    {
        $filter = LeadFilter::make()
            ->name('Иванов')
            ->createdBy(1)
            ->updatedBy(2)
            ->createdAt(100, 200)
            ->closestTaskAt(300)
            ->customFieldsValues([123 => ['from' => 1]]);

        self::assertSame('Иванов', $filter->get('name'));
        self::assertSame(1, $filter->get('created_by'));
        self::assertSame(2, $filter->get('updated_by'));
        self::assertSame(['from' => 100, 'to' => 200], $filter->get('created_at'));
        self::assertSame(['from' => 300], $filter->get('closest_task_at'));
        self::assertSame([123 => ['from' => 1]], $filter->get('custom_fields_values'));
    }

    public function testPriceKeepsZeroLowerBound(): void
    {
        self::assertSame(['from' => 0, 'to' => 1000], LeadFilter::make()->price(0, 1000)->get('price'));
    }
}
