<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests\Traits;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadWith;
use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasLeadWithQuery
{
    /** @use HasWithQuery<LeadWith> */
    use HasWithQuery;

    public function withCatalogElements(): static
    {
        return $this->addWith(LeadWith::CATALOG_ELEMENTS);
    }

    public function withIsPriceModifiedByRobot(): static
    {
        return $this->addWith(LeadWith::IS_PRICE_MODIFIED_BY_ROBOT);
    }

    public function withLossReason(): static
    {
        return $this->addWith(LeadWith::LOSS_REASON);
    }

    public function withContacts(): static
    {
        return $this->addWith(LeadWith::CONTACTS);
    }

    public function withOnlyDeleted(): static
    {
        return $this->addWith(LeadWith::ONLY_DELETED);
    }

    public function withSourceId(): static
    {
        return $this->addWith(LeadWith::SOURCE_ID);
    }

    public function withSource(): static
    {
        return $this->addWith(LeadWith::SOURCE);
    }
}
