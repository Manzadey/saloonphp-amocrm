<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

use Manzadey\SaloonAmoCrm\Modules\Model;

class CustomFieldModel extends Model
{
    public function id(): ?int
    {
        return $this->get('field_id');
    }

    public function setId(int $id): static
    {
        return $this->add('field_id', $id);
    }

    public function name(): ?string
    {
        return $this->get('field_name');
    }

    public function code(): ?string
    {
        return $this->get('field_code');
    }

    public function setCode(string $code): static
    {
        return $this->add('field_code', $code);
    }

    public function type(): ?string
    {
        return $this->get('field_type');
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
     * @return array<CustomFieldValueModel>
     */
    public function values(): array
    {
        return array_map(
            static fn(array $value): CustomFieldValueModel => new CustomFieldValueModel($value),
            $this->get('values', [])
        );
    }

    /**
     * @param array<CustomFieldValueModel|array> $values
     * @return $this
     */
    public function setValues(array $values): static
    {
        return $this->add(
            key: 'values',
            value: array_map(
                static fn(CustomFieldValueModel|array $value): array => $value instanceof CustomFieldValueModel ?
                    $value->all() :
                    $value,
                $values
            )
        );
    }

    public function addValue(mixed $value): static
    {
        if (is_null($value)) {
            return $this;
        }

        $values = $this->values();

        if ($value instanceof CustomFieldValueModel) {
            $value = $value->all();
        }

        if (is_string($value) || is_int($value) || is_bool($value)) {
            $value = compact('value');
        }

        $values[] = $value;

        return $this->setValues($values);
    }
}