<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Filters;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Makeable;

/**
 * Только те ключи, которые amoCRM принимает у каждой сущности с фильтрами:
 * `id`, `responsible_user_id`, `updated_at`. Всё остальное объявляется в
 * подклассе — иначе база раздаёт методы, которые API молча игнорирует.
 */
abstract class AbstractFilter extends ArrayStore
{
    use Makeable;

    public function range(string $name, string|int|null $from = null, string|int|null $to = null): static
    {
        return $this->add($name, array_filter(
            compact('from', 'to'),
            static fn (string|int|null $value): bool => $value !== null,
        ));
    }

    /**
     * @param array<int>|int $id
     */
    public function id(array|int $id): static
    {
        return $this->add('id', $id);
    }

    /**
     * @param array<int>|int $responsibleUserId
     */
    public function responsibleUserId(array|int $responsibleUserId): static
    {
        return $this->add('responsible_user_id', $responsibleUserId);
    }

    public function updatedAt(?int $from = null, ?int $to = null): static
    {
        return $this->range('updated_at', $from, $to);
    }
}
