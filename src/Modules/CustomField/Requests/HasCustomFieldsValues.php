<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField\Requests;

use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;
use Saloon\Repositories\ArrayStore;

/**
 * @mixin ArrayStore
 */
trait HasCustomFieldsValues
{
    /**
     * @return array<CustomFieldModel>
     */
    public function customFieldsValues(): array
    {
        return array_map(
            static fn(array $customField): CustomFieldModel => new CustomFieldModel($customField),
            $this->get('custom_fields_values', []),
        );
    }

    /**
     * @param array<CustomFieldModel|array> $fields
     * @return $this
     */
    public function setCustomFieldsValues(array $fields): static
    {
        return $this->add(
            key: 'custom_fields_values',
            value: array_map(
                static fn(CustomFieldModel|array $field): array => $field instanceof CustomFieldModel ?
                    $field->all() :
                    $field,
                $fields,
            )
        );
    }

    public function addCustomFieldsValue(CustomFieldModel|array $field): static
    {
        $fields = $this->customFieldsValues();
        $fields[] = $field;

        return $this->setCustomFieldsValues($fields);
    }
}