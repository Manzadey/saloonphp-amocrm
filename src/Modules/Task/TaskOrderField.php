<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task;

use Manzadey\SaloonAmoCrm\Query\OrderField;

enum TaskOrderField: string implements OrderField
{
    case CREATED_AT = 'created_at';

    case COMPLETE_TILL = 'complete_till';

    case ID = 'id';
}
