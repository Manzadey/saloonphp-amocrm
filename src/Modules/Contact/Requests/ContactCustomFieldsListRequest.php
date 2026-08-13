<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Requests;

use Manzadey\SaloonAmoCrm\Modules\CustomField\Responses\CustomFieldsListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class ContactCustomFieldsListRequest extends AbstractContactRequest
{
    use SendsTypedResponse;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Query\HasFilterQuery;
    use Query\HasOrderQuery;

    protected Method $method = Method::GET;

    protected ?string $response = CustomFieldsListResponse::class;

    protected string $endpoint = '/contacts/custom_fields';

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): CustomFieldsListResponse
    {
        return $this->sendTyped(CustomFieldsListResponse::class);
    }
}
