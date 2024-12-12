<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Account;

enum AccountWithQueryEnum: string
{
    case AMOJO_ID = 'amojo_id';

    case AMOJO_RIGHTS = 'amojo_rights';

    case USERS_GROUPS = 'users_groups';

    case TASK_TYPES = 'task_types';

    case VERSION = 'version';

    case ENTITY_NAMES = 'entity_names';

    case DATETIME_SETTINGS = 'datetime_settings';

    case DRIVE_URL = 'drive_url';

    case IS_API_FILTER_ENABLED = 'is_api_filter_enabled';

    case INVOICES_SETTINGS = 'invoices_settings';
}
