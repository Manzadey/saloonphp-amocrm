<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Requests\Traits;

use Manzadey\SaloonAmoCrm\Modules\Note\NoteWith;
use Manzadey\SaloonAmoCrm\Query\HasWithQuery;

trait HasNoteWithQuery
{
    /** @use HasWithQuery<NoteWith> */
    use HasWithQuery;

    public function withIsPinned(): static
    {
        return $this->addWith(NoteWith::IS_PINNED);
    }
}
