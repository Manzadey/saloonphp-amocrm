<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Requests\OAuth2;

use Manzadey\SaloonAmoCrm\Enum\GrandTypeEnum;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AccessToken extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/access_token';
    }

    public function setAuthCode(string $authCode): static
    {
        $this->body()->add('code', $authCode);

        return $this;
    }

    public function setRefreshToken(string $token): static
    {
        $this->body()->add('refresh_token', $token);

        return $this;
    }

    public function setGrandType(GrandTypeEnum $enum): static
    {
        $this->body()->add('grant_type', $enum->value);

        return $this;
    }

    public function exchangeAuthCode(string $code): static
    {
        return $this->setGrandType(GrandTypeEnum::AuthCode)->setAuthCode($code);
    }

    public function refreshAccessToken(string $token): static
    {
        return $this->setGrandType(GrandTypeEnum::RefreshToken)->setRefreshToken($token);
    }
}