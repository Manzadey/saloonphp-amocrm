<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadFilter;
use Manzadey\SaloonAmoCrm\Modules\Lead\Responses\LeadListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class LeadListRequest extends AbstractLeadRequest
{
    use SendsTypedResponse;
    use Traits\HasLeadOrderQuery;
    use Query\HasSearchQuery;
    use Traits\HasLeadWithQuery;
    /** @use Query\HasFilterQuery<LeadFilter> */
    use Query\HasFilterQuery;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;

    protected Method $method = Method::GET;

    protected ?string $response = LeadListResponse::class;

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): LeadListResponse
    {
        return $this->sendTyped(LeadListResponse::class);
    }
}
