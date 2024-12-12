<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class TagCreateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

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

    public function tag(TagModel|array $model): static
    {
        $this->body()->add(value: $model instanceof TagModel ? $model->all() : $model);

        return $this;
    }

    public function send(): Response
    {
        return $this->connector->send($this);
    }
}