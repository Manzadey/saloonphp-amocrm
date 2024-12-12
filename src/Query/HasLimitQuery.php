<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Saloon\Traits\RequestProperties\HasQuery;

/**
 * @mixin HasQuery
 */
trait HasLimitQuery
{
    public function limit(int $limit): static
    {
        $this->query()->add('limit', $limit);

        return $this;
    }
}