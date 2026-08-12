<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Responses\WebhookUnsubscribeResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * @see https://www.amocrm.ru/developers/content/crm_platform/webhooks-api
 */
class WebhookUnsubscribeRequest extends Request implements HasBody
{
    use HasJsonBody;
    use SendsTypedResponse;

    protected Method $method = Method::DELETE;

    protected ?string $response = WebhookUnsubscribeResponse::class;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $destination,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/webhooks';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return ['destination' => $this->destination];
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): WebhookUnsubscribeResponse
    {
        return $this->sendTyped(WebhookUnsubscribeResponse::class);
    }
}
