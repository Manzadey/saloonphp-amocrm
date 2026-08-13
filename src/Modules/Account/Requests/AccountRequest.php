<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Account\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Account\AccountWith;
use Manzadey\SaloonAmoCrm\Modules\Account\Responses\AccountResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class AccountRequest extends Request
{
    use SendsTypedResponse;
    use Traits\HasAccountWithQuery;

    protected Method $method = Method::GET;

    protected ?string $response = AccountResponse::class;

    /**
     * @param list<AccountWith>|null $with
     */
    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly ?array $with = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return '/account';
    }

    protected function defaultQuery(): array
    {
        if ($this->with === null) {
            return [];
        }

        return [
            'with' => implode(',', array_map(static fn (AccountWith $case): string => $case->value, $this->with)),
        ];
    }

    public function send(): AccountResponse
    {
        return $this->sendTyped(AccountResponse::class);
    }
}
