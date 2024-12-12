<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadFilter;
use Manzadey\SaloonAmoCrm\Modules\Lead\Responses\LeadListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class LeadListRequest extends AbstractLeadRequest
{
    use Query\HasOrderQuery;
    use Query\HasSearchQuery;
    use Traits\HasLeadWithQuery;
    use Query\HasFilterQuery;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;

    protected Method $method = Method::GET;

    protected ?string $response = LeadListResponse::class;

    public function addFilter(LeadFilter $filter): static
    {
        foreach ($filter->all() as $name => $value) {
            $this->filter($name, $value);
        }

        return $this;
    }

    /**
     * @return Response|LeadListResponse
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response|LeadListResponse
    {
        return parent::send();
    }
}