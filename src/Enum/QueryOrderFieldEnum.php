<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Enum;

/**
 * Объединение полей сортировки по всем сущностям, а не набор, валидный для каждой:
 * сделки принимают `created_at`/`updated_at`/`id`, задачи — `created_at`/`complete_till`/`id`.
 * Сортировка задач по `updated_at` (как и сделок по `complete_till`) будет API отклонена.
 */
enum QueryOrderFieldEnum: string
{
    case ID = 'id';

    case CREATED_AT = 'created_at';

    case UPDATED_AT = 'updated_at';

    case COMPLETE_TILL = 'complete_till';
}
