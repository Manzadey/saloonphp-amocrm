<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead;

use Manzadey\SaloonAmoCrm\Query\OrderField;

enum LeadOrderField: string implements OrderField
{
    case CREATED_AT = 'created_at';

    case UPDATED_AT = 'updated_at';

    case ID = 'id';
}
