<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Responses;

use Manzadey\SaloonAmoCrm\Modules\Note\NoteModel;
use Saloon\Http\Response;

class NoteItemResponse extends Response
{
    public function note(): ?NoteModel
    {
        return new NoteModel($this->json());
    }
}