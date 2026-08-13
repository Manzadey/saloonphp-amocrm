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
        $filter = $this->query()->get('filter', []);
        $filter[$key] = $value;

        $this->query()->add('filter', $filter);

        return $this;
    }
}
