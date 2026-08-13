<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Responses;

use Manzadey\SaloonAmoCrm\Modules\Webhook\WebhookModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class WebhookResponse extends Response
{
    use HasEmbeddedModels;

    public function webhook(): ?WebhookModel
    {
        return $this->single(WebhookModel::class);
    }
}
