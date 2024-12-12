<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Saloon\Http\Request;
use Saloon\Http\Response;

class AbstractContactRequest extends Request
{
    protected string $endpoint = '/contacts';

    public function __construct(
        protected readonly MainConnector $connector
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    public function send(): Response
    {
        return $this->connector->send($this);
    }
}