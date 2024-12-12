<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Connectors;

use Saloon\Contracts\Body\HasBody;
use Saloon\Http\Connector;
use Saloon\Traits\Body\HasJsonBody;

class OAuth2Connector extends Connector implements HasBody
{
    use HasJsonBody;

    public function __construct(
        protected readonly string $baseDomain,
        protected readonly string $clientId,
        protected readonly string $clientSecret,
        protected readonly string $redirectUri,
    )
    {
    }


    public function resolveBaseUrl(): string
    {
        return "https://$this->baseDomain/oauth2";
    }

    protected function defaultBody(): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri
        ];
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}