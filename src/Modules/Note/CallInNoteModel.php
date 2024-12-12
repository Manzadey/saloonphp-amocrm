<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

use Manzadey\SaloonAmoCrm\Modules\Model;

class CallInNoteModel extends Model
{
    public function uniq(): ?string
    {
        return $this->get('uniq');
    }

    public function setUniq(string $uniq): static
    {
        return $this->add('uniq', $uniq);
    }

    public function duration(): ?int
    {
        return $this->get('duration');
    }

    public function setDuration(int $duration): static
    {
        return $this->add('duration', $duration);
    }

    public function source(): ?string
    {
        return $this->get('source');
    }

    public function setSource(string $source): static
    {
        return $this->add('source', $source);
    }

    public function link(): ?string
    {
        return $this->get('link');
    }

    public function setLink(string $link): static
    {
        return $this->add('link', $link);
    }

    public function phone(): ?string
    {
        return $this->get('phone');
    }

    public function setPhone(string $phone): static
    {
        return $this->add('phone', $phone);
    }

    public function callResponsible(): ?string
    {
        return $this->get('call_responsible');
    }

    public function setCallResponsible(string $responsible): static
    {
        return $this->add('call_responsible', $responsible);
    }
}