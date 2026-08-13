<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

use Manzadey\SaloonAmoCrm\Query\WithField;

enum NoteWith: string implements WithField
{
    case IS_PINNED = 'is_pinned';
}
