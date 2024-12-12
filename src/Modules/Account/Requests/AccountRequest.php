<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Account\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Account\AccountWithQueryEnum;
use Manzadey\SaloonAmoCrm\Modules\Account\Responses\AccountResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class AccountRequest extends Request
{
    protected Method $method = Method::GET;

    protected ?string $response = AccountResponse::class;

    /**
     * @param array<string|AccountWithQueryEnum>|null $with
     */
    public function __construct(
        protected readonly MainConnector $connector,
        protected readonly ?array $with = null,
    )
    {
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
        if (is_array($this->with)) {
            return [
                'with' => implode(',', $this->with),
            ];
        }

        return [];
    }

    public function with(AccountWithQueryEnum|string $with): static
    {
        $query = $this->query()->get('with');
        $query = is_null($query) ? [] : explode(',', $query);
        $query[] = $with instanceof AccountWithQueryEnum ? $with->value : $with;
        $this->query()->add('with', implode(',', $query));

        return $this;
    }

    public function withAll(): static
    {
        $this->query()->add('with', null);

        foreach (AccountWithQueryEnum::cases() as $case) {
            $this->with($case);
        }

        return $this;
    }

    public function send(): Response|AccountResponse
    {
        return $this->connector->send($this);
    }
}