<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User;

use Manzadey\SaloonAmoCrm\Query\WithField;

enum UserWith: string implements WithField
{
    case ROLE = 'role';

    case GROUP = 'group';

    case UUID = 'uuid';

    case AMOJO_ID = 'amojo_id';

    case USER_RANK = 'user_rank';

    case PHONE_NUMBER = 'phone_number';
}
