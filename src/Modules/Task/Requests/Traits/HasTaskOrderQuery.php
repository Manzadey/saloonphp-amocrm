<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests\Traits;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderEnum;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskOrderField;
use Manzadey\SaloonAmoCrm\Query\HasOrderQuery;

trait HasTaskOrderQuery
{
    /** @use HasOrderQuery<TaskOrderField> */
    use HasOrderQuery;

    public function latest(TaskOrderField $field = TaskOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::DESC);
    }

    public function oldest(TaskOrderField $field = TaskOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::ASC);
    }
}
