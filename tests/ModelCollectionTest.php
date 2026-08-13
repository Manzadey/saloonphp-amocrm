<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Countable;
use IteratorAggregate;
use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Коллекция — единственная точка, где `mixed` из json() превращается в список
 * моделей. Отсюда требования: она обязана переваривать любой вход, а не только
 * ожидаемый список массивов.
 */
class ModelCollectionTest extends TestCase
{
    public function testBuildsModelsFromListOfArrays(): void
    {
        $collection = ModelCollection::of(LeadModel::class, [['id' => 1], ['id' => 2]]);

        self::assertCount(2, $collection);
        self::assertSame([1, 2], array_map(static fn (LeadModel $l): ?int => $l->id(), $collection->all()));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function nonListInputs(): array
    {
        return [
            'null'   => [null],
            'string' => ['oops'],
            'int'    => [0],
            'bool'   => [false],
            'empty'  => [[]],
        ];
    }

    #[DataProvider('nonListInputs')]
    public function testAnythingButAListYieldsEmptyCollection(mixed $items): void
    {
        $collection = ModelCollection::of(LeadModel::class, $items);

        self::assertTrue($collection->isEmpty());
        self::assertFalse($collection->isNotEmpty());
        self::assertNull($collection->first());
        self::assertSame([], $collection->all());
    }

    public function testNonArrayElementsAreDropped(): void
    {
        $collection = ModelCollection::of(LeadModel::class, [['id' => 1], 'мусор', null, ['id' => 2]]);

        self::assertCount(2, $collection);
        self::assertSame([0, 1], array_keys($collection->all()), 'ключи должны быть списком, а не дырявыми');
    }

    public function testKeysAreRenumberedFromAssociativeInput(): void
    {
        $collection = ModelCollection::of(LeadModel::class, ['a' => ['id' => 1], 'b' => ['id' => 2]]);

        self::assertSame([0, 1], array_keys($collection->all()));
    }

    public function testFirstReturnsTheHeadElement(): void
    {
        self::assertSame(
            1,
            ModelCollection::of(LeadModel::class, [['id' => 1], ['id' => 2]])->first()?->id()
        );
    }

    public function testIsIterable(): void
    {
        $ids = [];

        foreach (ModelCollection::of(LeadModel::class, [['id' => 7], ['id' => 8]]) as $lead) {
            $ids[] = $lead->id();
        }

        self::assertSame([7, 8], $ids);
    }

    public function testImplementsTheExpectedInterfaces(): void
    {
        $collection = ModelCollection::of(LeadModel::class, []);

        self::assertInstanceOf(IteratorAggregate::class, $collection);
        self::assertInstanceOf(Countable::class, $collection);
    }
}
