<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Configs;

use Closure;
use League\OAuth2\Client\Token\AccessToken;
use Saloon\Traits\Makeable;

class TokenConfig
{
    use Makeable;

    protected ?Closure $callbackRefreshAccessToken = null;

    protected ?Closure $callbackAuthorizeCode = null;

    protected ?Closure $callbackGetAccessToken = null;

    protected ?AccessToken $accessToken = null;

    public function getAccessToken(): AccessToken
    {
        if ($this->accessToken instanceof AccessToken) {
            return $this->accessToken;
        }

        $this->accessToken = new AccessToken(
            call_user_func($this->getCallbackGetAccessToken())
        );

        return $this->accessToken;
    }

    public function getCallbackGetAccessToken(): ?Closure
    {
        return $this->callbackGetAccessToken;
    }

    public function setCallbackGetAccessToken(Closure $callbackGetAccessToken): static
    {
        $this->callbackGetAccessToken = $callbackGetAccessToken;

        return $this;
    }

    public function getCallbackAuthorizeCode(): ?Closure
    {
        return $this->callbackAuthorizeCode;
    }

    public function setCallbackAuthorizeCode(?Closure $callbackAuthorizeCode): static
    {
        $this->callbackAuthorizeCode = $callbackAuthorizeCode;

        return $this;
    }

    public function getCallbackRefreshAccessToken(): ?Closure
    {
        $closure = $this->callbackRefreshAccessToken;

        $this->accessToken = null;

        return $closure;
    }

    public function setCallbackRefreshAccessToken(?Closure $callbackRefreshAccessToken): static
    {
        $this->callbackRefreshAccessToken = $callbackRefreshAccessToken;

        return $this;
    }
}