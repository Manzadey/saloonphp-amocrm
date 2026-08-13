<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Manzadey\SaloonAmoCrm\Modules\Contact\ContactModel;
use Manzadey\SaloonAmoCrm\Modules\Contact\Responses\ContactCreateResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class ContactCreateRequest extends AbstractContactRequest implements HasBody
{
    use SendsTypedResponse;
    use HasJsonBody;

    protected Method $method = Method::POST;

    protected ?string $response = ContactCreateResponse::class;

    /**
     * @param ContactModel|array<string, mixed> $model
     */
    public function add(ContactModel|array $model): static
    {
        if (is_array($model)) {
            $model = new ContactModel($model);
        }

        $this->body()->add(value: $model->all());

        return $this;
    }

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): ContactCreateResponse
    {
        return $this->sendTyped(ContactCreateResponse::class);
    }
}
