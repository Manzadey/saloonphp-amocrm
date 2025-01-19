<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Saloon\Enums\Method;

class ContactCustomFieldsListRequest extends AbstractContactRequest
{
    protected Method $method = Method::GET;

    protected string $endpoint = '/contacts/custom_fields';
}