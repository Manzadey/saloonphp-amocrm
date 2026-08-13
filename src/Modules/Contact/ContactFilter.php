<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact;

use Manzadey\SaloonAmoCrm\Filters\AbstractFilter;
use Manzadey\SaloonAmoCrm\Filters\HasCommonEntityFilters;

/**
 * Набор фильтров контактов совпадает с набором сделок минус `price`, `statuses`,
 * `pipeline_id` и `closed_at` — специфика воронки.
 */
class ContactFilter extends AbstractFilter
{
    use HasCommonEntityFilters;
}
