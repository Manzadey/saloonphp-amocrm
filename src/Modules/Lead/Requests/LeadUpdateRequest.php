<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Lead\Responses\LeadUpdateResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class LeadUpdateRequest extends AbstractLeadRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    protected ?string $response = LeadUpdateResponse::class;

    public function addLead(LeadModel|array $model): static
    {
        $this->body()->add(value: $model instanceof LeadModel ? $model->all() : $model);

        return $this;
    }

    public function addLeads(...$models): static
    {
        foreach ($models as $model) {
            $this->addLead($model);
        }

        return $this;
    }

    /**
     * @return Response|LeadUpdateResponse
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response|LeadUpdateResponse
    {
        return parent::send();
    }
}