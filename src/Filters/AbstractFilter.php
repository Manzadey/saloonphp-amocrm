<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Filters;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Makeable;

class AbstractFilter extends ArrayStore
{
    use Makeable;

    public function range(string $name, string|int $from = null, string|int|null $to = null): static
    {
        return $this->add($name, array_filter(compact('from', 'to')));
    }

    public function customFieldsValues(array $fields): static
    {
        return $this->add('custom_fields_values', $fields);
    }

    /**
     * @param array<int>|int $id
     * @return $this
     */
    public function id(array|int $id): static
    {
        return $this->add('id', $id);
    }

    /**
     * @param array<string>|string $name
     * @return $this
     */
    public function name(array|string $name): static
    {
        return $this->add('name', $name);
    }

    /**
     * @param array<int>|int $createdBy
     * @return $this
     */
    public function createdBy(array|int $createdBy): static
    {
        return $this->add('created_by', $createdBy);
    }

    /**
     * @param array<int>|int $updatedBy
     * @return $this
     */
    public function updatedBy(array|int $updatedBy): static
    {
        return $this->add('updated_by', $updatedBy);
    }

    /**
     * @param array<int>|int $responsibleUserId
     * @return $this
     */
    public function responsibleUserId(array|int $responsibleUserId): static
    {
        return $this->add('responsible_user_id', $responsibleUserId);
    }

    /**
     * @param int|null $from
     * @param int|null $to
     * @return $this
     */
    public function createdAt(int $from = null, int $to = null): static
    {
        return $this->range('created_at', $from, $to);
    }

    /**
     * @param int|null $from
     * @param int|null $to
     * @return $this
     */
    public function updatedAt(int $from = null, int $to = null): static
    {
        return $this->range('updated', $from, $to);
    }

    /**
     * @param int|null $from
     * @param int|null $to
     * @return $this
     */
    public function closestTaskAt(int $from = null, int $to = null): static
    {
        return $this->range('closest_task_at', $from, $to);
    }
}