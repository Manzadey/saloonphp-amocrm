<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Tests\Configs;

use Manzadey\SaloonAmoCrm\Configs\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected Config $config;

    protected function setUp(): void
    {
        $this->config = new Config(
            'domain.amocrm.ru',
            'https://example.com/callback',
            'client_id',
            'client_secret'
        );
    }

    public function testBaseDomain(): void
    {
        $this->assertEquals('domain.amocrm.ru', $this->config->getBaseDomain());
    }

    public function testRedirectUri(): void
    {
        $this->assertEquals('https://example.com/callback', $this->config->getRedirectUri());
    }

    public function testClientId(): void
    {
        $this->assertEquals('client_id', $this->config->getClientId());
    }

    public function testClientSecret(): void
    {
        $this->assertEquals('client_secret', $this->config->getClientSecret());
    }
}