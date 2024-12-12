<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm;

use Closure;
use JsonException;
use League\OAuth2\Client\Token\AccessToken;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Exceptions\AmoCrm\AmoCrmExchangeAuthCodeException;
use Manzadey\SaloonAmoCrm\Exceptions\AmoCrm\AmoCrmRefreshAccessTokenException;
use Manzadey\SaloonAmoCrm\Modules\Account\Requests\AccountRequest;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactReference;
use Manzadey\SaloonAmoCrm\Requests\OAuth2\AccessToken as AccessTokenRequest;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\AccessTokenAuthenticator;

class Client
{
    protected MainConnector $connector;

    public function __construct(
        private readonly Configs\Config $config,
        protected Configs\TokenConfig $tokenConfig,
    ) {
        $this->connector = new MainConnector($this->config->getBaseDomain(), $this->getAuth());
    }

    public function getConfig(): Configs\Config
    {
        return $this->config;
    }

    /**
     * @return void
     * @throws JsonException
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function authorize(): void
    {
        $response = $this->oAuth2()->send(
            AccessTokenRequest::make()->exchangeAuthCode(
                call_user_func(
                    $this->getTokenConfig()->getCallbackAuthorizeCode()
                )
            )
        );


        if ($response->clientError()) {
            throw new AmoCrmExchangeAuthCodeException(
                $response->json('hint') ?? $response->getPsrResponse()->getReasonPhrase()
            );
        }

        call_user_func(
            $this->getTokenConfig()->getCallbackRefreshAccessToken(),
            new AccessToken($response->json())
        );
    }

    /**
     * @return void
     * @throws JsonException
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function refreshAccessToken(): void
    {
        $response = $this->oAuth2()->send(
            AccessTokenRequest::make()->refreshAccessToken(
                $this->tokenConfig->getAccessToken()->getRefreshToken()
            )
        );

        if ($response->clientError()) {
            throw new AmoCrmRefreshAccessTokenException(
                $response->json('hint') ?? $response->getPsrResponse()->getReasonPhrase()
            );
        }

        call_user_func(
            $this->getTokenConfig()->getCallbackRefreshAccessToken(),
            new AccessToken($response->json())
        );
    }

    public function oAuth2(): Connectors\OAuth2Connector
    {
        return new Connectors\OAuth2Connector(
            baseDomain: $this->getConfig()->getBaseDomain(),
            clientId: $this->getConfig()->getClientId(),
            clientSecret: $this->getConfig()->getClientSecret(),
            redirectUri: $this->getConfig()->getRedirectUri(),
        );
    }

    public function getTokenConfig(): Configs\TokenConfig
    {
        return $this->tokenConfig;
    }


    public function getAuth(): Closure
    {
        return function() {
            $token = call_user_func($this->getTokenConfig()->getCallbackGetAccessToken());

            if ($token === null) {
                $this->authorize();
            }

            if ($this->getTokenConfig()->getAccessToken()->hasExpired()) {
                $this->refreshAccessToken();
            }

            return new AccessTokenAuthenticator(
                $this->getTokenConfig()->getAccessToken()->getToken()
            );
        };
    }

    public function connector(): MainConnector
    {
        return $this->connector;
    }

    /**
     * @param array<string|\Manzadey\SaloonAmoCrm\Modules\Account\AccountWithQueryEnum>|null $with
     * @return AccountRequest
     */
    public function account(?array $with = null): AccountRequest
    {
        return new AccountRequest($this->connector(), $with);
    }

    public function leads(): Modules\Lead\LeadReference
    {
        return new Modules\Lead\LeadReference($this->connector());
    }

    public function contacts(): ContactReference
    {
        return new ContactReference($this->connector());
    }
}