<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\User\Responses\UserItemResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

class UserItemRequest extends AbstractUserRequest
{
    use SendsTypedResponse;
    use Traits\HasUserWithQuery;

    protected Method $method = Method::GET;

    protected ?string $response = UserItemResponse::class;

    public function __construct(
        protected MainConnector $connector,
        protected int $id,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return $this->endpoint . '/' . $this->id;
    }

    public function send(): UserItemResponse
    {
        return $this->sendTyped(UserItemResponse::class);
    }
}
