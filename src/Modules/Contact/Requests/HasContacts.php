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
    public function contacts(): array
    {
        return array_map(
            static fn(array $contact): ContactModel => new ContactModel($contact),
            $this->get('_embedded.contacts', [])
        );
    }

    /**
     * @param array<ContactModel|array> $contacts
     * @return $this
     */
    public function setContacts(array $contacts): static
    {
        return $this->add(
            key: '_embedded',
            value: array_merge_recursive($this->get('_embedded', []), [
                'contacts' => array_map(
                    callback: static fn(ContactModel|array $value): array => array_intersect_key(
                        ($value instanceof ContactModel ? $value->all() : $value),
                        array_flip(['id', 'is_main'])
                    ),
                    array: $contacts
                )
            ])
        );
    }

    public function addContact(ContactModel|array $contact): static
    {
        $contacts = $this->contacts();
        $contacts[] = $contact;

        return $this->setContacts($contacts);
    }
}