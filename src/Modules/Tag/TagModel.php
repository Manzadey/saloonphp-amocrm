<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag;

use Manzadey\SaloonAmoCrm\Modules\Model;

class TagModel extends Model
{
    public function id(): ?int
    {
        return $this->get('id');
    }

    public function setId(int $id): static
    {
        return $this->add('id', $id);
    }

    public function name(): ?string
    {
        return $this->get('name');
    }

    public function setName(string $name): static
    {
        return $this->add('name', $name);
    }

    public function color(): ?string
    {
        return $this->get('color');
    }

    public function setColor(TagColorEnum|string|null $color = null): static
    {
        if ($color instanceof TagColorEnum) {
            $color = $color->value;
        }

        return $this->add('color', $color);
    }
}