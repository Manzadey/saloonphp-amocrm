<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Requests;

use Manzadey\SaloonAmoCrm\Modules\Webhook\Responses\WebhookListResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

/**
 * @see https://www.amocrm.ru/developers/content/crm_platform/webhooks-api
 */
class WebhookListRequest extends AbstractWebhookRequest
{
    use SendsTypedResponse;

    protected Method $method = Method::GET;

    protected ?string $response = WebhookListResponse::class;

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): WebhookListResponse
    {
        return $this->sendTyped(WebhookListResponse::class);
    }
}
