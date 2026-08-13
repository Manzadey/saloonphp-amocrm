<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Makeable;

/**
 * Примечания не наследуют `AbstractFilter`: из его набора они принимают только
 * `id` и `updated_at`, а `responsible_user_id` — нет.
 */
class NoteFilter extends ArrayStore
{
    use Makeable;

    /**
     * @param array<int>|int $id
     */
    public function id(array|int $id): static
    {
        return $this->add('id', $id);
    }

    /**
     * @param array<int>|int $entityId
     */
    public function entityId(array|int $entityId): static
    {
        return $this->add('entity_id', $entityId);
    }

    /**
     * @param array<NoteTypeEnum>|NoteTypeEnum $type
     */
    public function noteType(array|NoteTypeEnum $type): static
    {
        if ($type instanceof NoteTypeEnum) {
            return $this->add('note_type', $type->value);
        }

        return $this->add('note_type', array_map(
            static fn (NoteTypeEnum $case): string => $case->value,
            $type,
        ));
    }

    public function updatedAt(?int $from = null, ?int $to = null): static
    {
        return $this->add('updated_at', array_filter(
            compact('from', 'to'),
            static fn (?int $value): bool => $value !== null,
        ));
    }
}
