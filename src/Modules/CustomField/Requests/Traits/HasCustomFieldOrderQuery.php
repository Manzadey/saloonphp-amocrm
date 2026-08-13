<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\Traits;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderEnum;
use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldOrderField;
use Manzadey\SaloonAmoCrm\Query\HasOrderQuery;

trait HasCustomFieldOrderQuery
{
    /** @use HasOrderQuery<CustomFieldOrderField> */
    use HasOrderQuery;

    public function latest(CustomFieldOrderField $field = CustomFieldOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::DESC);
    }

    public function oldest(CustomFieldOrderField $field = CustomFieldOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::ASC);
    }
}
