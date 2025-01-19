<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Task\Responses\TaskItemResponse;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class TaskItemRequest extends AbstractTaskRequest
{
    protected Method $method = Method::GET;

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
    public function send(): Response|TaskItemResponse
    {
        return parent::send();
    }
}