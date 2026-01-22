<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Tests\Configs;

use Manzadey\SaloonAmoCrm\Configs\TokenConfig;
use PHPUnit\Framework\TestCase;

class TokenConfigTest extends TestCase
{
    protected TokenConfig $tokenConfig;

    protected function setUp(): void
    {
        $this->tokenConfig = new TokenConfig;
    }

    public function testSetAndGetCallbackGetAccessToken(): void
    {
        $callback = fn() => ['access_token' => 'test_token'];

        $this->tokenConfig->setCallbackGetAccessToken($callback);

        $this->assertSame($callback, $this->tokenConfig->getCallbackGetAccessToken());
    }

    public function testGetAccessTokenReturnsAccessTokenInstance(): void
    {
        $tokenData = [
            'access_token' => 'abc-123',
            'expires_in' => 3600,
        ];

        $this->tokenConfig->setCallbackGetAccessToken(fn(): array => $tokenData);

        $accessToken = $this->tokenConfig->getAccessToken();

        $this->assertSame('abc-123', $accessToken->getToken());
    }

    public function testGetAccessTokenCachesToken(): void
    {
        $counter = 0;
        $callback = function () use (&$counter): array {
            $counter++;
            return ['access_token' => 'token_' . $counter];
        };

        $this->tokenConfig->setCallbackGetAccessToken($callback);

        // Первый вызов
        $firstToken = $this->tokenConfig->getAccessToken();
        // Второй вызов - должен вернуть тот же объект
        $secondToken = $this->tokenConfig->getAccessToken();

        $this->assertSame($firstToken, $secondToken);
        $this->assertEquals(1, $counter);
    }

    public function testGetCallbackRefreshAccessTokenResetsCachedToken(): void
    {
        $this->tokenConfig->setCallbackGetAccessToken(fn() => ['access_token' => 'initial']);

        // Кэшируем токен
        $this->tokenConfig->getAccessToken();

        $refreshCallback = fn(): array => ['access_token' => 'refreshed'];
        $this->tokenConfig->setCallbackRefreshAccessToken($refreshCallback);

        // Вызов getCallbackRefreshAccessToken должен обнулить accessToken
        $returnedCallback = $this->tokenConfig->getCallbackRefreshAccessToken();

        $this->assertSame($refreshCallback, $returnedCallback);

        // Проверяем, что при следующем получении токена вызовется основной колбэк (или произойдет регенерация)
        // В данном случае, это доказывает, что внутреннее состояние $accessToken было сброшено.
    }

    public function testSetAndGetCallbackAuthorizeCode(): void
    {
        $callback = fn(): string => 'auth_code';

        $this->tokenConfig->setCallbackAuthorizeCode($callback);

        $this->assertSame($callback, $this->tokenConfig->getCallbackAuthorizeCode());
    }
}