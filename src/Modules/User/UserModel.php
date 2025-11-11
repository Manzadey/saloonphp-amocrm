<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\User;

use Manzadey\SaloonAmoCrm\Modules\Model;

class UserModel extends Model
{
    public function id(): ?int
    {
        return $this->get('id');
    }

    public function setId(int $id): static
    {
        return $this->add('id', $id);
    }

    public function name(): ?int
    {
        return $this->get('name');
    }

    public function setName(string $name): static
    {
        return $this->add('name', $name);
    }

    public function email(): ?string
    {
        return $this->get('email');
    }

    public function setEmail(string $email): static
    {
        return $this->add('email', $email);
    }

    public function lang(): ?string
    {
        return $this->get('lang');
    }

    public function setLang(string $lang): static
    {
        return $this->add('lang', $lang);
    }

    public function rights(): ?array
    {
        return $this->get('rights');
    }

    public function setRights(array $rights): static
    {
        return $this->add('rights', $rights);
    }
}