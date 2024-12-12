<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Lead\Responses\LeadItemResponse;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class LeadItemRequest extends AbstractLeadRequest
{
    use Traits\HasLeadWithQuery;

    protected Method $method = Method::GET;

    protected ?string $response = LeadItemResponse::class;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly int $id,
    ) {
        parent::__construct($connector);
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return "/leads/$this->id";
    }

    protected function defaultQuery(): array
    {
        return [
            'limit' => 1,
        ];
    }

    /**
     * @return Response|LeadItemResponse
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response|LeadItemResponse
    {
        return $this->connector->send($this);
    }
}