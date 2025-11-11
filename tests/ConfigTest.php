<?php

declare(strict_types=1);

namespace Manzadey\tests;

use Manzadey\SaloonAmoCrm\Configs\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected Config $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
    }

    public function testBaseDomain(): void
    {
        $this->config->expects($this->once())
            ->method('getBaseDomain')
            ->willReturn('domain.amocrm.ru');
        $this->assertEquals('domain.amocrm.ru', $this->config->getBaseDomain());
    }

    public function testRedirectUri(): void
    {
        $this->config->expects($this->once())
            ->method('getRedirectUri')
            ->willReturn('https://example.com/callback');
        $this->assertEquals('https://example.com/callback', $this->config->getRedirectUri());
    }

    public function testClientId(): void
    {
        $this->config->expects($this->once())
            ->method('getClientId')
            ->willReturn('client_id');
        $this->assertEquals('client_id', $this->config->getClientId());
    }

    public function testClientSecret(): void
    {
        $this->config->expects($this->once())
            ->method('getClientSecret')
            ->willReturn('client_secret');
        $this->assertEquals('client_secret', $this->config->getClientSecret());
    }
}