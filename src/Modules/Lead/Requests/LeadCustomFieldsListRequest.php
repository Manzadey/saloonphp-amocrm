<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldFilter;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Requests\Traits\HasCustomFieldOrderQuery;
use Manzadey\SaloonAmoCrm\Modules\CustomField\Responses\CustomFieldsListResponse;
use Manzadey\SaloonAmoCrm\Query\HasFilterQuery;
use Manzadey\SaloonAmoCrm\Query\HasLimitQuery;
use Manzadey\SaloonAmoCrm\Query\HasPageQuery;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class LeadCustomFieldsListRequest extends AbstractLeadRequest
{
    use SendsTypedResponse;
    use HasPageQuery;
    use HasLimitQuery;
    use HasCustomFieldOrderQuery;
    /** @use HasFilterQuery<CustomFieldFilter> */
    use HasFilterQuery;

    protected Method $method = Method::GET;

    protected ?string $response = CustomFieldsListResponse::class;

    protected string $endpoint = '/leads/custom_fields';

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): CustomFieldsListResponse
    {
        return $this->sendTyped(CustomFieldsListResponse::class);
    }
}
