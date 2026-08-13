<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Requests;

use Manzadey\SaloonAmoCrm\Modules\User\Responses\UserListResponse;
use Manzadey\SaloonAmoCrm\Query;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class UserListRequest extends AbstractUserRequest
{
    use SendsTypedResponse;
    use Query\HasPageQuery;
    use Query\HasLimitQuery;
    use Traits\HasUserWithQuery;

    protected Method $method = Method::GET;

    protected ?string $response = UserListResponse::class;

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): UserListResponse
    {
        return $this->sendTyped(UserListResponse::class);
    }
}
