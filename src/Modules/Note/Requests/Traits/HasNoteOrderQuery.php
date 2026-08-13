<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Requests\Traits;

use Manzadey\SaloonAmoCrm\Enum\QueryOrderEnum;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteOrderField;
use Manzadey\SaloonAmoCrm\Query\HasOrderQuery;

trait HasNoteOrderQuery
{
    /** @use HasOrderQuery<NoteOrderField> */
    use HasOrderQuery;

    public function latest(NoteOrderField $field = NoteOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::DESC);
    }

    public function oldest(NoteOrderField $field = NoteOrderField::ID): static
    {
        return $this->order($field, QueryOrderEnum::ASC);
    }
}
