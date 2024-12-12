<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Responses;

use Manzadey\SaloonAmoCrm\Modules\Contact\ContactModel;
use Saloon\Http\Response;

class ContactCreateResponse extends Response
{
    /**
     * @return array
     * @throws \JsonException
     */
    public function contacts(): array
    {
        return array_map(
            static fn(array $contact): ContactModel => new ContactModel($contact),
            $this->json('_embedded.contacts', [])
        );
    }

    /**
     * @return array
     * @throws \JsonException
     */
    public function contactsIds(): array
    {
        return array_map(
            static fn(ContactModel $contact): int => $contact->id(),
            $this->contacts(),
        );
    }
}