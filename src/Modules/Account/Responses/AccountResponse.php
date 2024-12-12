<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Account\Responses;

use Saloon\Http\Response;

class AccountResponse extends Response
{
    /**
     * @throws \JsonException
     */
    public function getId(): int
    {
        return $this->json('id');
    }

    /**
     * @throws \JsonException
     */
    public function getName(): string
    {
        return $this->json('name');
    }

    /**
     * @throws \JsonException
     */
    public function getSubdomain(): string
    {
        return $this->json('subdomain');
    }

    /**
     * @throws \JsonException
     */
    public function getCreatedAt(): int
    {
        return $this->json('created_at');
    }

    /**
     * @throws \JsonException
     */
    public function getCreatedBy(): int
    {
        return $this->json('created_by');
    }

    /**
     * @throws \JsonException
     */
    public function getUpdatedAt(): int
    {
        return $this->json('updated_at');
    }

    /**
     * @throws \JsonException
     */
    public function getUpdatedBy(): int
    {
        return $this->json('updated_by');
    }

    /**
     * @throws \JsonException
     */
    public function getCurrentUserId(): int
    {
        return $this->json('current_user_id');
    }

    /**
     * @throws \JsonException
     */
    public function getCountry(): string
    {
        return $this->json('country');
    }

    /**
     * @throws \JsonException
     */
    public function getCurrency(): string
    {
        return $this->json('currency');
    }

    /**
     * @throws \JsonException
     */
    public function getCurrencySymbol(): string
    {
        return $this->json('currency_symbol');
    }
}