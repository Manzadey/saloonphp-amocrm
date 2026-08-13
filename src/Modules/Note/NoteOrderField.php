<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

use Manzadey\SaloonAmoCrm\Query\OrderField;

enum NoteOrderField: string implements OrderField
{
    case UPDATED_AT = 'updated_at';

    case ID = 'id';
}
