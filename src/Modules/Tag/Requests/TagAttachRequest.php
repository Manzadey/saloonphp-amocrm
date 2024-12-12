<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Contracts\HasTagsContract;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class TagAttachRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly string $entityType,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return "/$this->entityType";
    }

    public function model(HasTagsContract $model): static
    {
        $this->body()->add(value: $model->all());

        return $this;
    }

    public function send(): Response
    {
        return $this->connector->send($this);
    }
}