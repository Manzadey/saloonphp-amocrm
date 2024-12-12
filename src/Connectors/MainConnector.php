<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Connectors;

use Closure;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class MainConnector extends Connector
{
    use AlwaysThrowOnErrors;

    public function __construct(
        protected readonly string $baseDomain,
        protected readonly Closure $auth,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveBaseUrl(): string
    {
        return "https://$this->baseDomain/api/v4";
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function defaultAuth(): ?Authenticator
    {
        return call_user_func($this->auth);
    }
}