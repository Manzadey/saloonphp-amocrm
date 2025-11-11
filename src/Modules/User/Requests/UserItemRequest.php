<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Saloon\Enums\Method;

class UserItemRequest extends AbstractUserRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        protected MainConnector $connector,
        protected int $id,
    )
    {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return $this->endpoint . '/' . $this->id;
    }

    public function with(array $values): static
    {
        $this->query()->add('with', implode(',', $values));

        return $this;
    }
}