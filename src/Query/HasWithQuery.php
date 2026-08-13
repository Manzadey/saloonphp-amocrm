<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use Saloon\Traits\RequestProperties\HasQuery;

/**
 * @template TWith of WithField
 *
 * @mixin HasQuery
 */
trait HasWithQuery
{
    /**
     * @param list<TWith> $with
     */
    public function with(array $with): static
    {
        $this->query()->add('with', implode(',', array_map(
            static fn (WithField $field): string => (string) $field->value,
            $with,
        )));

        return $this;
    }

    /**
     * @param TWith $value
     */
    public function addWith(WithField $value): static
    {
        $current = $this->query()->get('with');
        $values = is_string($current) && $current !== '' ? explode(',', $current) : [];
        $values[] = (string) $value->value;

        $this->query()->add('with', implode(',', array_unique($values)));

        return $this;
    }
}
