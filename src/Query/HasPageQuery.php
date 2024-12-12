<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Saloon\Traits\RequestProperties\HasQuery;

/**
 * @mixin HasQuery
 */
trait HasPageQuery
{
    public function page(int $page): static
    {
        $this->query()->add('page', $page);

        return $this;
    }
}