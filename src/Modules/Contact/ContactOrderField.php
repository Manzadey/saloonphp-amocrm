<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact;

use Manzadey\SaloonAmoCrm\Query\OrderField;

enum ContactOrderField: string implements OrderField
{
    case UPDATED_AT = 'updated_at';

    case ID = 'id';
}
