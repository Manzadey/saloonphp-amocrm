<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead;

use Manzadey\SaloonAmoCrm\Filters\AbstractFilter;
use Manzadey\SaloonAmoCrm\Filters\HasCommonEntityFilters;

class LeadFilter extends AbstractFilter
{
    use HasCommonEntityFilters;

    public function price(int $priceFrom, ?int $priceTo = null): static
    {
        return $this->range('price', $priceFrom, $priceTo);
    }

    /**
     * @param list<array{pipeline_id: int|null, status_id: int}> $statuses
     */
    public function statuses(array $statuses): static
    {
        return $this->add('statuses', $statuses);
    }

    /**
     * @param array<int>|int $statusId
     */
    public function addStatus(array|int $statusId, ?int $pipelineId = null): static
    {
        $statuses = $this->get('statuses', []);
        $pipelineIdToUse = $pipelineId ?? $this->get('pipeline_id');

        foreach (is_array($statusId) ? $statusId : [$statusId] as $id) {
            $statuses[] = [
                'pipeline_id' => $pipelineIdToUse,
                'status_id' => $id,
            ];
        }

        return $this->statuses($statuses);
    }

    /**
     * @param array<int>|int $pipeline
     */
    public function pipelineId(array|int $pipeline): static
    {
        return $this->add('pipeline_id', $pipeline);
    }

    public function closedAt(?int $from = null, ?int $to = null): static
    {
        return $this->range('closed_at', $from, $to);
    }
}
