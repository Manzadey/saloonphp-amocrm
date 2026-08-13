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
            $request->addFilter(TaskFilter::make()->entityType($this->entityType));
        }

        return $request;
    }

    public function item(int $id): Requests\TaskItemRequest
    {
        return new Requests\TaskItemRequest($this->connector, $id);
    }

    public function create(TaskModel $model): Requests\TaskCreateRequest
    {
        return (new Requests\TaskCreateRequest($this->connector))->add($model);
    }
}
