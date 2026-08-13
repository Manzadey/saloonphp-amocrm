<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Contracts;

use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;

interface CustomFieldsValuesContract
{
    /**
     * @return list<CustomFieldModel>
     */
    public function customFieldsValues(): array;

    /**
     * @param list<CustomFieldModel|array<string, mixed>> $fields
     */
    public function setCustomFieldsValues(array $fields): static;
}
