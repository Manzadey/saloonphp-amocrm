<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

class YclIdFieldModel extends CustomFieldModel
{
    /** @var array<string, mixed> */
    protected array $defaults = [
        'field_code' => 'YCLID',
    ];
}
