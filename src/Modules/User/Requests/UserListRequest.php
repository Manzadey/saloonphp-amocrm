<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Requests;

use Saloon\Enums\Method;

class UserListRequest extends AbstractUserRequest
{
    protected Method $method = Method::GET;
}