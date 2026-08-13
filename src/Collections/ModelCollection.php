<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Manzadey\SaloonAmoCrm\Modules\Model;

/**
 * @template T of Model
 *
 * @implements IteratorAggregate<int, T>
 */
class ModelCollection implements IteratorAggregate, Countable
{
    /**
     * @param list<T> $models
     */
    private function __construct(private readonly array $models)
    {
    }

    /**
     * Единственная точка, где `mixed` из `json()` становится списком моделей: amoCRM
     * отдаёт то список, то 204 с пустым телом, а `_embedded.<ключ>` может вообще
     * отсутствовать. Что не похоже на данные сущности — отбрасывается здесь, чтобы
     * вызывающему не приходилось проверять каждый элемент.
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel> $model
     * @param  mixed                $items сырое значение из тела ответа
     * @return self<TModel>
     */
    public static function of(string $model, mixed $items): self
    {
        if (! is_array($items)) {
            return new self([]);
        }

        $models = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $models[] = new $model($item);
            }
        }

        return new self($models);
    }

    /**
     * @return T|null
     */
    public function first(): ?Model
    {
        return $this->models[0] ?? null;
    }

    /**
     * @return list<T>
     */
    public function all(): array
    {
        return $this->models;
    }

    public function isEmpty(): bool
    {
        return $this->models === [];
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function count(): int
    {
        return count($this->models);
    }

    /**
     * @return ArrayIterator<int, T>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->models);
    }
}
