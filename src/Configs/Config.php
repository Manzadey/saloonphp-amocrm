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

    /**
     * @return string Домен в amoCRM
     */
    public function getBaseDomain(): string
    {
        return $this->baseDomain;
    }

    /**
     * @return string Redirect URI указанный в настройках интеграции. Должен четко совпадать с тем, что указан в настройках
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @return string ID интеграции
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string Секрет интеграции
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}