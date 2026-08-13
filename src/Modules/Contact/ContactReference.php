<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;

class ContactReference
{
    public function __construct(
        protected readonly MainConnector $connector
    ) {
    }

    public function list(): Requests\ContactListRequest
    {
        return new Requests\ContactListRequest($this->connector);
    }

    public function search(string $query): Requests\ContactListRequest
    {
        return $this->list()->querySearch($query);
    }

    public function create(ContactModel $model): Requests\ContactCreateRequest
    {
        return (new Requests\ContactCreateRequest($this->connector))->add($model);
    }

    public function customFields(): Requests\ContactCustomFieldsListRequest
    {
        return new Requests\ContactCustomFieldsListRequest($this->connector);
    }
}
