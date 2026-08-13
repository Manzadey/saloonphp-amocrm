<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Manzadey\SaloonAmoCrm\Modules\Contact\ContactModel;
use Saloon\Repositories\ArrayStore;

/**
 * @mixin ArrayStore
 */
trait HasContacts
{
    /**
     * @return list<ContactModel>
     */
    public function contacts(): array
    {
        return array_map(
            static fn (array $contact): ContactModel => new ContactModel($contact),
            $this->get('_embedded.contacts', [])
        );
    }

    /**
     * @param list<ContactModel|array<string, mixed>> $contacts
     * @return $this
     */
    public function setContacts(array $contacts): static
    {
        $embedded = $this->get('_embedded', []);

        $embedded['contacts'] = array_map(
            static fn (ContactModel|array $value): array => array_intersect_key(
                ($value instanceof ContactModel ? $value->all() : $value),
                array_flip(['id', 'is_main'])
            ),
            $contacts,
        );

        return $this->add('_embedded', $embedded);
    }

    /**
     * @param ContactModel|array<string, mixed> $contact
     */
    public function addContact(ContactModel|array $contact): static
    {
        $contacts = $this->contacts();
        $contacts[] = $contact;

        return $this->setContacts($contacts);
    }
}
