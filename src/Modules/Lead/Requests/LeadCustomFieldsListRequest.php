<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Modules\Lead\Responses\LeadCustomFieldsListResponse;
use Manzadey\SaloonAmoCrm\Query\HasFilterQuery;
use Manzadey\SaloonAmoCrm\Query\HasLimitQuery;
use Manzadey\SaloonAmoCrm\Query\HasOrderQuery;
use Manzadey\SaloonAmoCrm\Query\HasPageQuery;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class LeadCustomFieldsListRequest extends AbstractLeadRequest
{
    use HasPageQuery;
    use HasLimitQuery;
    use HasOrderQuery;
    use HasFilterQuery;

    protected Method $method = Method::GET;

    protected ?string $response = LeadCustomFieldsListResponse::class;

    protected string $endpoint = '/leads/custom_fields';

    /**
     * @return Response|LeadCustomFieldsListResponse
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response|LeadCustomFieldsListResponse
    {
        return parent::send();
    }
}