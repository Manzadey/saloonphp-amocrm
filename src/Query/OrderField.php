<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use BackedEnum;

/**
 * Общая граница для generic-параметра HasOrderQuery. Набор полей сортировки у каждой
 * сущности свой (сделки — `created_at`/`updated_at`/`id`, задачи —
 * `created_at`/`complete_till`/`id`, кастом-поля — `sort`/`id`), и единый енам на всех
 * пропускал комбинации, которые API отклоняет. Теперь чужое поле не проходит по типам.
 */
interface OrderField extends BackedEnum
{
}
