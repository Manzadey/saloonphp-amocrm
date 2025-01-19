<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;

class TaskReference
{
    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly ?string $entityType = null,
    ) {
    }

    public function list(): Requests\TaskListRequest
    {
        $request = new Requests\TaskListRequest($this->connector);

        if ($this->entityType !== null) {
            $request->filter('entity_type', $this->entityType);
        }

        return $request;
    }

    public function item(int $id): Requests\TaskItemRequest
    {
        return new Requests\TaskItemRequest($this->connector, $id);
    }

    public function create(TaskModel $model = null): Requests\TaskCreateRequest
    {
        $request = new Requests\TaskCreateRequest($this->connector);

        if ($model instanceof TaskModel) {
            $request->add($model);
        }

        return $request;
    }
}