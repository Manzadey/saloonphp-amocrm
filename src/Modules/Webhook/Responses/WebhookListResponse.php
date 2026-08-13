<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Webhook\WebhookModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class WebhookListResponse extends Response
{
    use HasEmbeddedModels;

    /**
     * amoCRM отдаёт 204 No Content вместо пустого списка, когда подписок нет —
     * `ModelCollection::of()` превращает такое тело в пустую коллекцию.
     *
     * @return ModelCollection<WebhookModel>
     */
    public function webhooks(): ModelCollection
    {
        return $this->embedded('webhooks', WebhookModel::class);
    }
}
