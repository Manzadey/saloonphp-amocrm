<?php

declare(strict_types=1);

namespace Manzadey\tests;

use League\OAuth2\Client\Token\AccessToken;
use Manzadey\SaloonAmoCrm\Configs\TokenConfig;
use PHPUnit\Framework\TestCase;

class TokenConfigTest extends TestCase
{
    protected TokenConfig $tokenConfig;

    protected function setUp(): void
    {
        $this->tokenConfig = new TokenConfig;
    }

    public function testCallbackGetAccessToken(): void
    {
        $this->assertNull($this->tokenConfig->getCallbackGetAccessToken());

        $accessToken = [
            'access_token' => '1234567890',
            'expires' => time() + 3600
        ];
        $this->tokenConfig->setCallbackGetAccessToken(fn() => $accessToken);
        $this->assertIsCallable($this->tokenConfig->getCallbackGetAccessToken());
        $this->assertEquals($accessToken, call_user_func($this->tokenConfig->getCallbackGetAccessToken()));
    }

    public function testCallbackAuthorizeCode(): void
    {
        $this->assertNull($this->tokenConfig->getCallbackAuthorizeCode());

        $this->tokenConfig->setCallbackAuthorizeCode(fn() => '1234567890');
        $this->assertIsCallable($this->tokenConfig->getCallbackAuthorizeCode());
        $this->assertEquals('1234567890', call_user_func($this->tokenConfig->getCallbackAuthorizeCode()));
    }

    public function testCallbackRefreshAccessToken(): void
    {
        $this->assertNull($this->tokenConfig->getCallbackRefreshAccessToken());
        $this->tokenConfig->setCallbackRefreshAccessToken(fn() => 'token');
        $this->assertIsCallable($this->tokenConfig->getCallbackRefreshAccessToken());
        $this->assertEquals('token', call_user_func($this->tokenConfig->getCallbackRefreshAccessToken()));
    }

    public function testAccessToken(): void
    {
        $token = [
            'access_token' => '1234567890',
            'expires' => time() + 3600
        ];

        $this->tokenConfig->setCallbackGetAccessToken(fn() => $token);

        $accessToken = $this->tokenConfig->getAccessToken();
        $this->assertSame($token['access_token'], $accessToken->getToken());
        $this->assertSame($token['expires'], $accessToken->getExpires());
    }
}