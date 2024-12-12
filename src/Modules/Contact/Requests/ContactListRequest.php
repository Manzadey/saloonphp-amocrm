<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Manzadey\SaloonAmoCrm\Modules\Contact\Responses\ContactListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class ContactListRequest extends AbstractContactRequest
{
    use HasContactWithQuery;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Query\HasSearchQuery;
    use Query\HasFilterQuery;
    use Query\HasOrderQuery;

    protected Method $method = Method::GET;

    protected ?string $response = ContactListResponse::class;

    public function send(): Response|ContactListResponse
    {
        return $this->connector->send($this);
    }
}