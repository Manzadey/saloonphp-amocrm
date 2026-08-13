<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Responses;

use Manzadey\SaloonAmoCrm\Modules\Webhook\WebhookModel;
use Saloon\Http\Response;

class WebhookResponse extends Response
{
    /**
     * @throws \JsonException
     */
    public function webhook(): ?WebhookModel
    {
        $data = $this->json();

        if (empty($data)) {
            return null;
        }

        return new WebhookModel($data);
    }
}
