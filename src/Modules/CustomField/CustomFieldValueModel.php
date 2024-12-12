<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

use Manzadey\SaloonAmoCrm\Modules\Model;

class CustomFieldValueModel extends Model
{
    public function value(): ?string
    {
        return $this->get('value');
    }

    public function setValue(string|bool|int|array $value): static
    {
        return $this->add('value', $value);
    }

    public function enumId(): ?int
    {
        return $this->get('enum_id');
    }

    public function setEnumId(int $id): static
    {
        return $this->add('enum_id', $id);
    }

    public function enumCode(): ?string
    {
        return $this->get('enum_code');
    }

    public function setEnumCode(string $code): static
    {
        return $this->add('enum_code', $code);
    }
}