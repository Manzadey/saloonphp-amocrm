<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Manzadey\SaloonAmoCrm\Modules\Contact\ContactFilter;
use Manzadey\SaloonAmoCrm\Modules\Contact\Responses\ContactListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class ContactListRequest extends AbstractContactRequest
{
    use SendsTypedResponse;
    use Traits\HasContactWithQuery;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Query\HasSearchQuery;
    /** @use Query\HasFilterQuery<ContactFilter> */
    use Query\HasFilterQuery;
    use Traits\HasContactOrderQuery;

    protected Method $method = Method::GET;

    protected ?string $response = ContactListResponse::class;

    public function send(): ContactListResponse
    {
        return $this->sendTyped(ContactListResponse::class);
    }
}
