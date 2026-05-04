<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Account\Responses;

use Saloon\Http\Response;

/**
 * Поля, помеченные `nullable`, возвращают `null`, если они отсутствуют в ответе AmoCRM
 * (например, не были запрошены через `with` или не предоставлены тарифом).
 */
final class AccountResponse extends Response
{
    public function getId(): ?int
    {
        return $this->json('id');
    }

    public function getName(): ?string
    {
        return $this->json('name');
    }

    public function getSubdomain(): ?string
    {
        return $this->json('subdomain');
    }

    public function getCreatedAt(): ?int
    {
        return $this->json('created_at');
    }

    public function getCreatedBy(): ?int
    {
        return $this->json('created_by');
    }

    public function getUpdatedAt(): ?int
    {
        return $this->json('updated_at');
    }

    public function getUpdatedBy(): ?int
    {
        return $this->json('updated_by');
    }

    public function getCurrentUserId(): ?int
    {
        return $this->json('current_user_id');
    }

    public function getCountry(): ?string
    {
        return $this->json('country');
    }

    public function getCurrency(): ?string
    {
        return $this->json('currency');
    }

    public function getCurrencySymbol(): ?string
    {
        return $this->json('currency_symbol');
    }
}