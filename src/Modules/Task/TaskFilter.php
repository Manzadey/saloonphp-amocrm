<?php

namespace Manzadey\SaloonAmoCrm\Modules\Task;

use Manzadey\SaloonAmoCrm\Filters\AbstractFilter;

class TaskFilter extends AbstractFilter
{
    public function isCompleted(bool $completed = true): static
    {
        $this->add('is_completed', $completed);

        return $this;
    }

    public function taskType(array|int|TaskTypeEnum $type): static
    {
        if (is_int($type)) {
            return $this->add('task_type', $type);
        }

        if (is_array($type)) {
            return $this->add(
                'task_type',
                array_map(
                    static fn(int|TaskTypeEnum $item) => $item instanceof TaskTypeEnum ? $item->value : $item,
                    $type
                )
            );
        }

        return $this;
    }

    public function entityType(string $type): static
    {
        $this->add('entity_type', $type);

        return $this;
    }

    /**
     * @param array<int>|int $id
     * @return $this
     */
    public function entityId(array|int $id): static
    {
        return $this->add('entity_id', $id);
    }
}