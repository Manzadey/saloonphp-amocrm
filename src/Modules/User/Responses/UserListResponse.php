<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\User\UserModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class UserListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<UserModel>
     */
    public function users(): ModelCollection
    {
        return $this->embedded('users', UserModel::class);
    }
}
