<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Filters;

/**
 * Ключи, общие для сделок и контактов, но не для всех сущностей: задачи их не
 * принимают, поэтому в `AbstractFilter` им места нет (см. AF2). Трейт, а не общий
 * базовый класс, — чтобы не выстраивать иерархию под один совпавший набор.
 *
 * @mixin AbstractFilter
 */
trait HasCommonEntityFilters
{
    /**
     * @param array<string>|string $name
     */
    public function name(array|string $name): static
    {
        return $this->add('name', $name);
    }

    /**
     * @param array<int>|int $createdBy
     */
    public function createdBy(array|int $createdBy): static
    {
        return $this->add('created_by', $createdBy);
    }

    /**
     * @param array<int>|int $updatedBy
     */
    public function updatedBy(array|int $updatedBy): static
    {
        return $this->add('updated_by', $updatedBy);
    }

    public function createdAt(?int $from = null, ?int $to = null): static
    {
        return $this->range('created_at', $from, $to);
    }

    public function closestTaskAt(?int $from = null, ?int $to = null): static
    {
        return $this->range('closest_task_at', $from, $to);
    }

    /**
     * @param array<int|string, mixed> $fields ключ — id кастомного поля
     */
    public function customFieldsValues(array $fields): static
    {
        return $this->add('custom_fields_values', $fields);
    }
}
