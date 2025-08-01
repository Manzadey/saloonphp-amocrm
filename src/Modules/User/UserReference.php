<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;

class UserReference
{
    public function __construct(
        protected readonly MainConnector $connector
    )
    {
    }

    public function list(): Requests\UserListRequest
    {
        return new Requests\UserListRequest($this->connector);
    }
}