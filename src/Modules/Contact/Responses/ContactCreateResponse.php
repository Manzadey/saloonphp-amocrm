<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class ContactCreateResponse extends Response
{
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<ContactModel>
     */
    public function contacts(): ModelCollection
    {
        return $this->embedded('contacts', ContactModel::class);
    }

    /**
     * @return list<int>
     */
    public function contactsIds(): array
    {
        $ids = [];

        foreach ($this->contacts() as $contact) {
            if (($id = $contact->id()) !== null) {
                $ids[] = $id;
            }
        }

        return $ids;
    }
}
