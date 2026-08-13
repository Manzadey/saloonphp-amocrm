<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead;

use Manzadey\SaloonAmoCrm\Filters\AbstractFilter;

class LeadFilter extends AbstractFilter
{
    /**
     * @param int $priceFrom
     * @param int|null $priceTo     */
    public function price(int $priceFrom, ?int $priceTo = null): static
    {
        return $this->range('price', $priceFrom, $priceTo);
    }

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

    public function statuses(array $statuses): static
    {
        return $this->add('statuses', $statuses);
    }

    public function addStatus(array|int $statusId, ?int $pipelineId = null): static
    {
        $statuses = $this->get('statuses', []);
        $pipelineIdToUse = $pipelineId ?? $this->get('pipeline_id');

        if (is_array($statusId)) {
            foreach ($statusId as $id) {
                $statuses[] = [
                    'pipeline_id' => $pipelineIdToUse,
                    'status_id' => $id
                ];
            }
        } else {
            $statuses[] = [
                'pipeline_id' => $pipelineIdToUse,
                'status_id' => $statusId
            ];
        }

        return $this->statuses($statuses);
    }

    /**
     * @param array<int>|int $pipeline     */
    public function pipelineId(array|int $pipeline): static
    {
        return $this->add('pipeline_id', $pipeline);
    }

    /**
     * @param int|null $from
     * @param int|null $to     */
    public function closedAt(?int $from = null, ?int $to = null): static
    {
        return $this->range('closed_at', $from, $to);
    }
}
