<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Requests\Traits;

use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasUserWithQuery
{
    use HasWithQuery;

    public function withRole(): static
    {
        return $this->addWith('role');
    }

    public function withGroup(): static
    {
        return $this->addWith('group');
    }

    public function withUuid(): static
    {
        return $this->addWith('uuid');
    }

    public function withAmojoId(): static
    {
        return $this->addWith('amojo_id');
    }

    public function withUserRank(): static
    {
        return $this->addWith('user_rank');
    }

    public function withPhoneNumber(): static
    {
        return $this->addWith('phone_number');
    }
}
