<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests;

use Manzadey\SaloonAmoCrm\Modules\Task\Responses\TaskCreateResponse;
use Manzadey\SaloonAmoCrm\Modules\Task\TaskModel;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class TaskCreateRequest extends AbstractTaskRequest implements HasBody
{
    use SendsTypedResponse;
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected ?string $response = TaskCreateResponse::class;

    /**
     * @param TaskModel|array<string, mixed> $model
     */
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
    public function send(): TaskCreateResponse
    {
        return $this->sendTyped(TaskCreateResponse::class);
    }
}
