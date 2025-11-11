<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Contracts;

interface CustomFieldsValuesContract
{
    public function customFieldsValues(): array;

    public function setCustomFieldsValues(array $fields): static;
}