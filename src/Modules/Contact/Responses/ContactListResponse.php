<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Contact\ContactModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class ContactListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<ContactModel>
     */
    public function contacts(): ModelCollection
    {
        return $this->embedded('contacts', ContactModel::class);
    }
}
