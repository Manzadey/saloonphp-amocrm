<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Saloon\Traits\RequestProperties\HasQuery;

/**
 * @mixin HasQuery
 */
trait HasSearchQuery
{
    public function querySearch(string|int $query): static
    {
        $this->query()->add('query', $query);

        return $this;
    }
}