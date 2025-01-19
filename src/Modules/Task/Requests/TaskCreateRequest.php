<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests;

use Manzadey\SaloonAmoCrm\Modules\Task\Responses\TaskCreateResponse;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskModel;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class TaskCreateRequest extends AbstractTaskRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected ?string $response = TaskCreateResponse::class;

    public function add(TaskModel|array $model): static
    {
        $this->body()->add(
            value: $model instanceof TaskModel ? $model->all() : $model
        );

        return $this;
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response|TaskCreateResponse
    {
        return parent::send();
    }
}