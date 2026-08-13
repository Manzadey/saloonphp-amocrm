<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class TaskListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<TaskModel>
     */
    public function tasks(): ModelCollection
    {
        return $this->embedded('tasks', TaskModel::class);
    }
}
