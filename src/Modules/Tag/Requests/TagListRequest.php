<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Query;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class TagListRequest extends Request
{
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Query\HasFilterQuery;
    use Query\HasSearchQuery;


    protected Method $method = Method::GET;

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
        return "/$this->entityType/tags";
    }

    public function filterName(string $name): static
    {
        return $this->filter('name', $name);
    }

    /**
     * @param array<int>|int $id
     * @return $this
     */
    public function filterId(array|int $id): static
    {
        return $this->filter('id', $id);
    }

    public function send(): Response
    {
        return $this->connector->send($this);
    }
}