<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;

class ContactReference
{
    public function __construct(
        protected MainConnector $connector
    )
    {
    }

    public function list(): Requests\ContactListRequest
    {
        return new Requests\ContactListRequest($this->connector);
    }

    public function search(string $query): Requests\ContactListRequest
    {
        return $this->list()->querySearch($query);
    }

    public function create(ContactModel $contactModel = null): Requests\ContactCreateRequest
    {
        $request = new Requests\ContactCreateRequest($this->connector);

        if ($contactModel instanceof ContactModel) {
            $request->add($contactModel);
        }

        return $request;
    }

    public function customFields(): Requests\ContactCustomFieldsListRequest
    {
        return new Requests\ContactCustomFieldsListRequest($this->connector);
    }
}