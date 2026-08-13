<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests\Traits;

use Manzadey\SaloonAmoCrm\Modules\Contact\ContactWith;
use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasContactWithQuery
{
    /** @use HasWithQuery<ContactWith> */
    use HasWithQuery;

    public function withCatalogElements(): static
    {
        return $this->addWith(ContactWith::CATALOG_ELEMENTS);
    }

    public function withLeads(): static
    {
        return $this->addWith(ContactWith::LEADS);
    }

    public function withCustomers(): static
    {
        return $this->addWith(ContactWith::CUSTOMERS);
    }
}
