<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Enum;

enum QueryOrderFieldEnum: string
{
    case ID = 'id';

    case CREATED_AT = 'created_at';

    case UPDATED_AT = 'updated_at';

    case SORT = 'sort';
}
