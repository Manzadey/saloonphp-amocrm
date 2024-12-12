<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Lead\Responses\LeadAddResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class LeadCreateRequest extends AbstractLeadRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected ?string $response = LeadAddResponse::class;

    public function add(LeadModel|array $model): static
    {
        if (is_array($model)) {
            $model = new LeadModel($model);
        }

        $this->body()->add(value: $model->all());
        
        return $this;
    }

    /**
     * @return Response|LeadAddResponse
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): Response|LeadAddResponse
    {
        return parent::send();
    }
}