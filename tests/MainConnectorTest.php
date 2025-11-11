<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Closure;
use DateTimeImmutable;
use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use PHPUnit\Framework\TestCase;
use Saloon\Http\Auth\AccessTokenAuthenticator;

class MainConnectorTest extends TestCase
{
    protected MainConnector $mainConnector;

    protected string $baseDomain = 'test.amocrm.ru';

    protected Closure $auth;

    protected function setUp(): void
    {
        $dateTime = new DateTimeImmutable();

        $this->auth = static fn() => new AccessTokenAuthenticator(
            'access_token',
            'refresh_token',
            $dateTime
        );

        $this->mainConnector = new MainConnector($this->baseDomain, $this->auth);
    }

    public function testResolveBaseUrl(): void
    {
        $this->assertEquals("https://$this->baseDomain/api/v4", $this->mainConnector->resolveBaseUrl());
    }

    public function testDefaultHeaders(): void
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $this->assertEquals($headers, $this->mainConnector->headers()->all());
    }

    public function testDefaultAuth(): void
    {
        $this->assertEquals(
            call_user_func($this->auth),
            $this->mainConnector->getAuthenticator()
        );
    }
}