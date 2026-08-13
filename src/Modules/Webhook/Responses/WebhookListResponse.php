<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Responses;

use Manzadey\SaloonAmoCrm\Modules\Webhook\WebhookModel;
use Saloon\Http\Response;

class WebhookListResponse extends Response
{
    /**
     * amoCRM returns 204 No Content instead of an empty list when the
     * account has no webhooks subscribed; Saloon's json() already falls
     * back to an empty array for an empty body, so no extra handling is
     * needed here.
     *
     * @return array<WebhookModel>
     * @throws \JsonException
     */
    public function webhooks(): array
    {
        return array_map(
            static fn (array $webhook): WebhookModel => new WebhookModel($webhook),
            $this->json('_embedded.webhooks', [])
        );
    }

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isEmpty(): bool
    {
        return empty($this->webhooks());
    }

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }
}
