<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Responses\CustomFieldsListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class CustomFieldListRequest extends Request
{
    use SendsTypedResponse;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Query\HasFilterQuery;
    use Query\HasOrderQuery;

    protected Method $method = Method::GET;

    protected ?string $response = CustomFieldsListResponse::class;

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
        return "/$this->entityType/custom_fields";
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): CustomFieldsListResponse
    {
        return $this->sendTyped(CustomFieldsListResponse::class);
    }
}
