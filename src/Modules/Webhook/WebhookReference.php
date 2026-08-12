<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;

class WebhookReference
{
    public function __construct(
        protected readonly MainConnector $connector
    ) {
    }

    public function list(): Requests\WebhookListRequest
    {
        return new Requests\WebhookListRequest($this->connector);
    }

    /**
     * @param array<string> $settings
     */
    public function subscribe(string $destination, array $settings): Requests\WebhookSubscribeRequest
    {
        return new Requests\WebhookSubscribeRequest($this->connector, $destination, $settings);
    }

    public function unsubscribe(string $destination): Requests\WebhookUnsubscribeRequest
    {
        return new Requests\WebhookUnsubscribeRequest($this->connector, $destination);
    }
}
