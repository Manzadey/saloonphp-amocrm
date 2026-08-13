<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Task\Responses\TaskItemResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class TaskItemRequest extends AbstractTaskRequest
{
    use SendsTypedResponse;

    protected Method $method = Method::GET;

    protected ?string $response = TaskItemResponse::class;

    public function __construct(
        protected MainConnector $connector,
        protected readonly int $id,
    ) {
        parent::__construct($connector);
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return "$this->endpoint/$this->id";
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): TaskItemResponse
    {
        return $this->sendTyped(TaskItemResponse::class);
    }
}
