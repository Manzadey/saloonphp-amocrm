<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderEnum;
use Manzadey\SaloonAmoCrm\Enum\QueryOrderFieldEnum;
use Saloon\Traits\RequestProperties\HasQuery;


/**
 * @mixin HasQuery
 */
trait HasOrderQuery
{
    public function order(QueryOrderFieldEnum $field, QueryOrderEnum $order): static
    {
        $this->query()->add('order', [
            $field->value => $order->value
        ]);

        return $this;
    }

    public function newest(QueryOrderFieldEnum $field = QueryOrderFieldEnum::ID): static
    {
        return $this->order($field, QueryOrderEnum::ASC);
    }

    public function latest(QueryOrderFieldEnum $field = QueryOrderFieldEnum::ID): static
    {
        return $this->order($field, QueryOrderEnum::DESC);
    }

    public function removeOrder(): static
    {
        $this->query()->remove('order');

        return $this;
    }
}