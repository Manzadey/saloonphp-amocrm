<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Enum;

/**
 * Объединение полей сортировки по всем сущностям, а не набор, валидный для каждой:
 * сделки принимают `created_at`/`updated_at`/`id`, контакты и примечания —
 * `updated_at`/`id`, задачи — `created_at`/`complete_till`/`id`, кастом-поля —
 * `sort`/`id`. Комбинацию из чужой сущности (задачи по `updated_at`, сделки по
 * `sort`) енам не отсекает — её отклонит API.
 */
enum QueryOrderFieldEnum: string
{
    case ID = 'id';

    case CREATED_AT = 'created_at';

    case UPDATED_AT = 'updated_at';

    case COMPLETE_TILL = 'complete_till';

    case SORT = 'sort';
}
