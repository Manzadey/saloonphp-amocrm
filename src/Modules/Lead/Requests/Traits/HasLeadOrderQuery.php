<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests\Traits;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderEnum;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadOrderField;
use Manzadey\SaloonAmoCrm\Query\HasOrderQuery;

trait HasLeadOrderQuery
{
    /** @use HasOrderQuery<LeadOrderField> */
    use HasOrderQuery;

    public function latest(LeadOrderField $field = LeadOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::DESC);
    }

    public function oldest(LeadOrderField $field = LeadOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::ASC);
    }
}
