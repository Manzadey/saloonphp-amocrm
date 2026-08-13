<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField\Requests;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;
use Saloon\Repositories\ArrayStore;

/**
 * @mixin ArrayStore
 */
trait HasCustomFieldsValues
{
    /**
     * @return list<CustomFieldModel>
     */
    public function customFieldsValues(): array
    {
        return ModelCollection::of(CustomFieldModel::class, $this->get('custom_fields_values'))->all();
    }

    /**
     * @param list<CustomFieldModel|array<string, mixed>> $fields
     * @return $this
     */
    public function setCustomFieldsValues(array $fields): static
    {
        return $this->add(
            key: 'custom_fields_values',
            value: array_map(
                static fn (CustomFieldModel|array $field): array => $field instanceof CustomFieldModel ?
                    $field->all() :
                    $field,
                $fields,
            )
        );
    }

    /**
     * @param CustomFieldModel|array<string, mixed> $field
     */
    public function addCustomFieldsValue(CustomFieldModel|array $field): static
    {
        $fields = $this->customFieldsValues();
        $fields[] = $field;

        return $this->setCustomFieldsValues($fields);
    }
}
