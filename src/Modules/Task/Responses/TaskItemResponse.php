<?php

namespace Manzadey\SaloonAmoCrm\Modules\Task\Responses;

use Manzadey\SaloonAmoCrm\Modules\Task\TaskModel;
use Saloon\Http\Response;

class TaskItemResponse extends Response
{
    /**
     * @throws \JsonException
     */
    public function task(): ?TaskModel
    {
        return empty($this->json()) ? null : new TaskModel($this->json());
    }
}