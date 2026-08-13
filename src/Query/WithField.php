<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Query;

use BackedEnum;

/**
 * Общая граница для generic-параметра HasWithQuery: набор значений `with` у каждой
 * сущности свой, а механика склейки в CSV одна. Наследование от `BackedEnum`
 * сужает реализации до енамов (обычный класс PHP не пустит) и оставляет `->value`
 * типизированным.
 */
interface WithField extends BackedEnum
{
}
