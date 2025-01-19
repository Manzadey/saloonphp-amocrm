<?php

namespace Manzadey\SaloonAmoCrm\Modules\Task\Responses;

use Manzadey\SaloonAmoCrm\Modules\Task\TaskModel;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class TaskListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;

    public function tasks(): array
    {
        return array_map(
            static fn(array $lead): TaskModel => new TaskModel($lead),
            $this->json('_embedded.tasks', [])
        );
    }

    public function task(): ?TaskModel
    {
        return $this->tasks()[0] ?? null;
    }
}