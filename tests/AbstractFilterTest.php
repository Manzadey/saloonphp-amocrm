<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Filters\AbstractFilter;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskFilter;
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

    public function testRangeKeepsZeroBound(): void
    {
        $filter = $this->filter()->range('price', 0, 200);

        self::assertSame(['from' => 0, 'to' => 200], $filter->get('price'));
    }

    public function testRangeDropsNullBound(): void
    {
        $filter = $this->filter()->range('price', 100, null);

        self::assertSame(['from' => 100], $filter->get('price'));
    }

    /**
     * Задачи наследуют от базы только то, что amoCRM у них принимает.
     *
     * @return array<string, array{string}>
     */
    public static function keysUnsupportedByTasks(): array
    {
        return [
            'name'                => ['name'],
            'createdBy'           => ['createdBy'],
            'updatedBy'           => ['updatedBy'],
            'createdAt'           => ['createdAt'],
            'closestTaskAt'       => ['closestTaskAt'],
            'customFieldsValues'  => ['customFieldsValues'],
        ];
    }

    /**
     * @dataProvider keysUnsupportedByTasks
     */
    public function testTaskFilterDoesNotInheritKeysTasksReject(string $method): void
    {
        self::assertFalse(
            method_exists(TaskFilter::class, $method),
            sprintf('TaskFilter не должен объявлять %s(): amoCRM не принимает этот фильтр у задач', $method)
        );
    }

    public function testTaskFilterKeepsTheKeysTasksAccept(): void
    {
        foreach (['id', 'responsibleUserId', 'updatedAt'] as $method) {
            self::assertTrue(method_exists(TaskFilter::class, $method), $method);
        }
    }
}
