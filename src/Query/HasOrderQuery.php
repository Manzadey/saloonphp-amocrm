<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderEnum;
use Saloon\Traits\RequestProperties\HasQuery;

/**
 * `latest()` / `oldest()` живут в пер-сущностных трейтах: их дефолтное поле — `::ID`
 * конкретного енама, которое generic-параметром не выразить.
 *
 * @template TField of OrderField
 *
 * @mixin HasQuery
 */
trait HasOrderQuery
{
    /**
     * @param TField $field
     */
    public function order(OrderField $field, QueryOrderEnum $order): static
    {
        $this->query()->add('order', [
            $field->value => $order->value,
        ]);

        return $this;
    }

    public function removeOrder(): static
    {
        $this->query()->remove('order');

        return $this;
    }
}
