<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests\Traits;

use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasLeadWithQuery
{
    use HasWithQuery;

    public function withCatalogElements(): static
    {
        return $this->addWith('catalog_elements');
    }

    public function withIsPriceModifiedByRobot(): static
    {
        return $this->addWith('is_price_modified_by_robot');
    }

    public function withLossReason(): static
    {
        return $this->addWith('loss_reason');
    }

    public function withContacts(): static
    {
        return $this->addWith('contacts');
    }

    public function withOnlyDeleted(): static
    {
        return $this->addWith('only_deleted');
    }

    public function withSourceId(): static
    {
        return $this->addWith('source_id');
    }
}