<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Model;

class CustomFieldModel extends Model
{
    /**
     * The `field_id`/`field_name`/`field_code`/`field_type` keys are how a
     * custom field looks *inside* an entity's `custom_fields_values`. The
     * `/{entity}/custom_fields` reference endpoint returns the same field as
     * a standalone object with plain `id`/`name`/`code`/`type` keys. Reading
     * both keeps the model usable for both shapes.
     */
    public function id(): ?int
    {
        return $this->get('field_id') ?? $this->get('id');
    }

    public function setId(int $id): static
    {
        return $this->add('field_id', $id);
    }

    public function name(): ?string
    {
        return $this->get('field_name') ?? $this->get('name');
    }

    public function code(): ?string
    {
        return $this->get('field_code') ?? $this->get('code');
    }

    public function setCode(string $code): static
    {
        return $this->add('field_code', $code);
    }

    public function type(): ?string
    {
        return $this->get('field_type') ?? $this->get('type');
    }

    public function accountId(): int
    {
        return $this->get('account_id');
    }

    public function sort(): string
    {
        return $this->get('sort');
    }

    public function isApiOnly(): bool
    {
        return $this->get('is_api_only');
    }

    public function link(): ?string
    {
        return $this->get('_links.self.href');
    }

    /**
     * @return list<CustomFieldValueModel>
     */
    public function values(): array
    {
        return ModelCollection::of(CustomFieldValueModel::class, $this->get('values'))->all();
    }

    /**
     * @param list<CustomFieldValueModel|array<string, mixed>> $values
     */
    public function setValues(array $values): static
    {
        return $this->add(
            key: 'values',
            value: array_map(
                static fn (CustomFieldValueModel|array $value): array => $value instanceof CustomFieldValueModel ?
                    $value->all() :
                    $value,
                $values
            )
        );
    }

    /**
     * Скаляр — сокращение для `['value' => …]`: у большинства полей значение одно, и
     * писать обёртку руками незачем. `float` в перечислении не для красоты — числовые
     * поля amoCRM отдают дробные значения.
     *
     * @param CustomFieldValueModel|array<string, mixed>|string|int|float|bool|null $value
     */
    public function addValue(CustomFieldValueModel|array|string|int|float|bool|null $value): static
    {
        if ($value === null) {
            return $this;
        }

        if (is_scalar($value)) {
            $value = ['value' => $value];
        }

        return $this->setValues([...$this->values(), $value]);
    }
}
