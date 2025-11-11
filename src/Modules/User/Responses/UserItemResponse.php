<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Responses;

use Manzadey\SaloonAmoCrm\Modules\User\UserModel;
use Saloon\Http\Response;

class UserItemResponse extends Response
{
    /**
     * @throws \JsonException
     */
    public function user(): ?UserModel
    {
        $data = $this->json();

        if (empty($data)) {
            return null;
        }

        return new UserModel($data);
    }
}