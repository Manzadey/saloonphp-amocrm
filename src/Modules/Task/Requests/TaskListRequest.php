<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests;

use Manzadey\SaloonAmoCrm\Modules\Task\Responses\TaskListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class TaskListRequest extends AbstractTaskRequest
{
    use SendsTypedResponse;
    use Query\HasOrderQuery;
    use Query\HasFilterQuery;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;

    protected Method $method = Method::GET;

    protected ?string $response = TaskListResponse::class;

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): TaskListResponse
    {
        return $this->sendTyped(TaskListResponse::class);
    }
}
