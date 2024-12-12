<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Makeable;

class CustomFieldFilter extends ArrayStore
{
    use Makeable;

    public function type(CustomFieldTypeEnum|string $type): static
    {
        $list = $this->get('type', []);

        if ($type instanceof CustomFieldTypeEnum) {
            $type = $type->value;
        }

        $list[] = $type;
        $this->add('type', $list);

        return $this;
    }
}