<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Responses;

use Manzadey\SaloonAmoCrm\Modules\Contact\ContactModel;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class ContactListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;

    public function contacts(): array
    {
        return array_map(
            static fn (array $contact) => new ContactModel($contact),
            $this->json('_embedded.contacts', [])
        );
    }

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isEmpty(): bool
    {
        return empty($this->contacts());
    }

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }
}