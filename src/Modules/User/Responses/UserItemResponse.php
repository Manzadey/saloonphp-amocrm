<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Responses;

use Manzadey\SaloonAmoCrm\Modules\User\UserModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class UserItemResponse extends Response
{
    use HasEmbeddedModels;

    public function user(): ?UserModel
    {
        return $this->single(UserModel::class);
    }
}
