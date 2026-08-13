<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Tag\Responses\TagCreateResponse;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class TagCreateRequest extends Request implements HasBody
{
    use SendsTypedResponse;
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected ?string $response = TagCreateResponse::class;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $entityType,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return "/$this->entityType/tags";
    }

    /**
     * @param TagModel|array<string, mixed> $model
     */
    public function add(TagModel|array $model): static
    {
        $this->body()->add(value: $model instanceof TagModel ? $model->all() : $model);

        return $this;
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): TagCreateResponse
    {
        return $this->sendTyped(TagCreateResponse::class);
    }
}
