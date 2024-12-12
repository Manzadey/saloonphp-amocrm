<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Saloon\Traits\RequestProperties\HasQuery;

/**
 * @mixin HasQuery
 */
trait HasFilterQuery
{
    public function filter(string $key, array|string|int $value): static
    {
        $this->query()->add(
            'filter',
            array_merge_recursive($this->query()->get('filter', []), [$key => $value])
        );

        return $this;
    }
}