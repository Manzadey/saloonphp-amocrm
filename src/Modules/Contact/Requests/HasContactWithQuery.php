<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasContactWithQuery
{
    use HasWithQuery;

    public function withCatalogElements(): static
    {
        return $this->addWith('catalog_elements');
    }

    public function withLeads(): static
    {
        return $this->addWith('leads');
    }

    public function withCustomers(): static
    {
        return $this->addWith('customers');
    }
}