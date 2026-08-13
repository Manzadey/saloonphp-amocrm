<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

use Manzadey\SaloonAmoCrm\Query\OrderField;

/**
 * `sort` — позиция поля в карточке; у списка кастом-полей это документированное поле
 * сортировки (`order[sort]=asc`), в отличие от остальных сущностей, где его нет.
 */
enum CustomFieldOrderField: string implements OrderField
{
    case SORT = 'sort';

    case ID = 'id';
}
