<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Configs;

use Saloon\Traits\Makeable;

class Config
{
    use Makeable;
    
    public function __construct(
        protected string $baseDomain,
        protected string $redirectUri,
        protected string $clientId,
        protected string $clientSecret,
    )
    {
    }

    public function getBaseDomain(): string
    {
        return $this->baseDomain;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}