<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Task\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class AbstractTaskRequest extends Request
{
    protected string $endpoint = '/tasks';

    public function __construct(
        protected MainConnector $connector,
        protected readonly ?string $entityType = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response
    {
        return $this->connector->send($this);
    }
}