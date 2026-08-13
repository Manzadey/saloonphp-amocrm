<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class AbstractPipelineRequest extends Request
{
    protected string $endpoint = '/leads/pipelines';

    public function __construct(
        protected readonly MainConnector $connector,
    ) {
    }

    /**
     * @inheritDoc
     */
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
