<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Query;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class CustomFieldListRequest extends Request
{
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Query\HasFilterQuery;
    use Query\HasOrderQuery;

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
        return "/$this->entityType/custom_fields";
    }
}