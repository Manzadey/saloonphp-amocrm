<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Makeable;

/**
 * У тегов всего два ключа фильтра, ни один не входит в `AbstractFilter`:
 * полнотекстовый поиск идёт отдельным параметром `query`, а не фильтром.
 */
class TagFilter extends ArrayStore
{
    use Makeable;

    /**
     * amoCRM принимает только одно точное название.
     */
    public function name(string $name): static
    {
        return $this->add('name', $name);
    }

    /**
     * @param array<int>|int $id
     */
    public function id(array|int $id): static
    {
        return $this->add('id', $id);
    }
}
