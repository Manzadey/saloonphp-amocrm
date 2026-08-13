<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Requests;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Modules\Lead\Responses\LeadUpdateResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class LeadUpdateRequest extends AbstractLeadRequest implements HasBody
{
    use SendsTypedResponse;
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    protected ?string $response = LeadUpdateResponse::class;

    /**
     * @param LeadModel|array<string, mixed> $model
     */
    public function add(LeadModel|array $model): static
    {
        $this->body()->add(value: $model instanceof LeadModel ? $model->all() : $model);

        return $this;
    }

    /**
     * @param LeadModel|array<string, mixed> ...$models
     */
    public function addMany(LeadModel|array ...$models): static
    {
        foreach ($models as $model) {
            $this->add($model);
        }

        return $this;
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): LeadUpdateResponse
    {
        return $this->sendTyped(LeadUpdateResponse::class);
    }
}
