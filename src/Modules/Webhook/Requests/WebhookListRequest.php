<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Responses\WebhookListResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * @see https://www.amocrm.ru/developers/content/crm_platform/webhooks-api
 */
class WebhookListRequest extends Request
{
    use SendsTypedResponse;

    protected Method $method = Method::GET;

    protected ?string $response = WebhookListResponse::class;

    public function __construct(
        protected readonly MainConnector $connector,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/webhooks';
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): WebhookListResponse
    {
        return $this->sendTyped(WebhookListResponse::class);
    }
}
