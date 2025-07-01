<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead;

use Manzadey\SaloonAmoCrm\Filters\AbstractFilter;

class LeadFilter extends AbstractFilter
{
    /**
     * @param int $priceFrom
     * @param int|null $priceTo
     * @return $this
     */
    public function price(int $priceFrom, int $priceTo = null): static
    {
        return $this->range('price', $priceFrom, $priceTo);
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
     * @param array<int>|int $pipeline
     * @return $this
     */
    public function pipelineId(array|int $pipeline): static
    {
        return $this->add('pipeline_id', $pipeline);
    }

    /**
     * @param int|null $from
     * @param int|null $to
     * @return $this
     */
    public function closedAt(int $from = null, int $to = null): static
    {
        return $this->range('closed_at', $from, $to);
    }
}