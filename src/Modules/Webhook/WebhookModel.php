<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Webhook;

use Manzadey\SaloonAmoCrm\Modules\Model;

class WebhookModel extends Model
{
    public function id(): ?int
    {
        return $this->get('id');
    }

    public function accountId(): ?int
    {
        return $this->get('account_id');
    }

    public function destination(): ?string
    {
        return $this->get('destination');
    }

    public function setDestination(string $destination): static
    {
        return $this->add('destination', $destination);
    }

    /**
     * @return array<string>
     */
    public function settings(): array
    {
        return $this->get('settings', []);
    }

    /**
     * @param array<string> $settings
     */
    public function setSettings(array $settings): static
    {
        return $this->add('settings', array_values($settings));
    }

    public function sort(): ?int
    {
        return $this->get('sort');
    }

    public function createdBy(): ?int
    {
        return $this->get('created_by');
    }

    public function createdAt(): ?int
    {
        return $this->get('created_at');
    }

    public function updatedAt(): ?int
    {
        return $this->get('updated_at');
    }

    /**
     * true, когда amoCRM сам отключил хук после серии неудачных доставок:
     * подписка в аккаунте остаётся, но события больше не отправляются.
     */
    public function isDisabled(): ?bool
    {
        return $this->get('disabled');
    }
}
