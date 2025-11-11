<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests;

use Manzadey\SaloonAmoCrm\Modules\Task\Responses\TaskListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class TaskListRequest extends AbstractTaskRequest
{
    use Query\HasOrderQuery;
    use Query\HasFilterQuery;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;

    protected Method $method = Method::GET;

    protected ?string $response = TaskListResponse::class;

    /**
     * @return Response|TaskListResponse
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response|TaskListResponse
    {
        return parent::send();
    }
}