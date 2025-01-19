<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

class ReferrerFieldModel extends CustomFieldModel
{
    protected array $defaults = [
        'field_code' => 'REFERRER',
    ];
}