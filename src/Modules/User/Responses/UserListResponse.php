<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Responses;

use Manzadey\SaloonAmoCrm\Modules\User\UserModel;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class UserListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;

    /**
     * @return array<UserModel>
     * @throws \JsonException
     */
    public function users(): array
    {
        return array_map(
            static fn (array $user): UserModel => new UserModel($user),
            $this->json('_embedded.users', [])
        );
    }

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isEmpty(): bool
    {
        return empty($this->users());
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
