<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Requests\Traits;

use Manzadey\SaloonAmoCrm\Modules\User\UserWith;
use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasUserWithQuery
{
    /** @use HasWithQuery<UserWith> */
    use HasWithQuery;

    public function withRole(): static
    {
        return $this->addWith(UserWith::ROLE);
    }

    public function withGroup(): static
    {
        return $this->addWith(UserWith::GROUP);
    }

    public function withUuid(): static
    {
        return $this->addWith(UserWith::UUID);
    }

    public function withAmojoId(): static
    {
        return $this->addWith(UserWith::AMOJO_ID);
    }

    public function withUserRank(): static
    {
        return $this->addWith(UserWith::USER_RANK);
    }

    public function withPhoneNumber(): static
    {
        return $this->addWith(UserWith::PHONE_NUMBER);
    }
}
