<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Account\Requests;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;
use Manzadey\SaloonAmoCrm\Modules\Account\AccountWithQueryEnum;
use Manzadey\SaloonAmoCrm\Modules\Account\Responses\AccountResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class AccountRequest extends Request
{
    protected Method $method = Method::GET;

    protected ?string $response = AccountResponse::class;

    /**
     * @var array<string> Уникальный список значений `with`, отдаваемых в query-строку.
     */
    private array $with = [];

    /**
     * @param MainConnector $connector
     * @param array<string|AccountWithQueryEnum>|null $with Список расширений ответа `with`.
     */
    public function __construct(
        private readonly MainConnector $connector,
        ?array $with = null,
    ) {
        if ($with !== null) {
            foreach ($with as $item) {
                $this->addWith($item);
            }
        }
    }

    public function resolveEndpoint(): string
    {
        return '/account';
    }

    protected function defaultQuery(): array
    {
        if ($this->with === []) {
            return [];
        }

        return ['with' => implode(',', $this->with)];
    }

    public function with(AccountWithQueryEnum|string $with): static
    {
        $this->addWith($with);

        return $this;
    }

    public function withAll(): static
    {
        $this->with = array_map(
            static fn(AccountWithQueryEnum $case): string => $case->value,
            AccountWithQueryEnum::cases(),
        );

        return $this;
    }

    public function send(): Response|AccountResponse
    {
        return $this->connector->send($this);
    }

    private function addWith(AccountWithQueryEnum|string $with): void
    {
        $value = $with instanceof AccountWithQueryEnum ? $with->value : $with;

        if (!in_array($value, $this->with, true)) {
            $this->with[] = $value;
        }
    }
}