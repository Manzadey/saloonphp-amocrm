<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests\Traits;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderEnum;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactOrderField;
use Manzadey\SaloonAmoCrm\Query\HasOrderQuery;

trait HasContactOrderQuery
{
    /** @use HasOrderQuery<ContactOrderField> */
    use HasOrderQuery;

    public function latest(ContactOrderField $field = ContactOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::DESC);
    }

    public function oldest(ContactOrderField $field = ContactOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::ASC);
    }
}
