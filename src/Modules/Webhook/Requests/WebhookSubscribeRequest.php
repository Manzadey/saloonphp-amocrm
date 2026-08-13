<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Webhook\Responses\WebhookResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Повторная подписка на тот же destination не создаёт дубль хука, а обновляет
 * settings у существующего — это поведение самого amoCRM.
 *
 * @see https://www.amocrm.ru/developers/content/crm_platform/webhooks-api
 */
class WebhookSubscribeRequest extends AbstractWebhookRequest implements HasBody
{
    use HasJsonBody;
    use SendsTypedResponse;

    protected Method $method = Method::POST;

    protected ?string $response = WebhookResponse::class;

    /**
     * @param array<string> $settings
     */
    public function __construct(
        MainConnector $connector,
        protected readonly string $destination,
        protected readonly array $settings,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'destination' => $this->destination,
            'settings' => array_values($this->settings),
        ];
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): WebhookResponse
    {
        return $this->sendTyped(WebhookResponse::class);
    }
}
