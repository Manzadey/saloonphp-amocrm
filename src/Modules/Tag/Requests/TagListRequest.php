<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Tag\Responses\TagListResponse;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagFilter;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class TagListRequest extends Request
{
    use SendsTypedResponse;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    /** @use Query\HasFilterQuery<TagFilter> */
    use Query\HasFilterQuery;
    use Query\HasSearchQuery;

    protected Method $method = Method::GET;

    protected ?string $response = TagListResponse::class;

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
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): TagListResponse
    {
        return $this->sendTyped(TagListResponse::class);
    }
}
