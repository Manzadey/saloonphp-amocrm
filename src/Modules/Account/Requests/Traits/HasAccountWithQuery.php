<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Account\Requests\Traits;

use Manzadey\SaloonAmoCrm\Modules\Account\AccountWith;
use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasAccountWithQuery
{
    /** @use HasWithQuery<AccountWith> */
    use HasWithQuery;

    /**
     * Аккаунт — единственный запрос, где запросить всё разом осмысленно: значения
     * не расширяют выборку, а добавляют независимые блоки настроек.
     */
    public function withAll(): static
    {
        return $this->with(AccountWith::cases());
    }
}
