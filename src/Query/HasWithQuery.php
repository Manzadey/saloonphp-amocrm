<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Saloon\Traits\RequestProperties\HasQuery;

/**
 * @mixin HasQuery
 */
trait HasWithQuery
{
    public function with(array $with): static
    {
        $this->query()->add('with', implode(',', $with));

        return $this;
    }

    public function addWith(string $value): static
    {
        $data = [];
        if (is_string($with = $this->query()->get('with'))) {
            $data = explode(',', $with);
        }

        $data[] = $value;
        $this->with(array_unique($data));

        return $this;
    }
}