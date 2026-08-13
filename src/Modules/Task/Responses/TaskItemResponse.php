<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Responses;

use Manzadey\SaloonAmoCrm\Modules\Task\TaskModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class TaskItemResponse extends Response
{
    use HasEmbeddedModels;

    public function task(): ?TaskModel
    {
        return $this->single(TaskModel::class);
    }
}
