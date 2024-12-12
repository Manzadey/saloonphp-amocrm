<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

use Manzadey\SaloonAmoCrm\Modules\Model;

class CommonNoteModel extends Model
{
    public function text(string $text): static
    {
        return $this->add('text', $text);
    }

    public function getText(): ?string
    {
        return $this->get('text');
    }
}