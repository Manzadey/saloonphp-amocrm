<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\RequestProperties\HasQuery;

/**
 * Наружу — только типизированный фильтр-объект своей сущности. Строковый `filter()`
 * позволял собрать ключ, которого у сущности нет: amoCRM такой фильтр молча
 * игнорирует и отдаёт весь список.
 *
 * @template TFilter of ArrayStore
 *
 * @mixin HasQuery
 */
trait HasFilterQuery
{
    /**
     * @param TFilter $filter
     */
    public function addFilter(ArrayStore $filter): static
    {
        foreach ($filter->all() as $key => $value) {
            $this->filter($key, $value);
        }

        return $this;
    }

    /**
     * @param array<mixed>|string|int|bool $value
     */
    protected function filter(string $key, array|string|int|bool $value): static
    {
        $filter = $this->query()->get('filter', []);
        $filter[$key] = $value;

        $this->query()->add('filter', $filter);

        return $this;
    }
}
