<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Responses;

use Manzadey\SaloonAmoCrm\Modules\Note\NoteModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class NoteItemResponse extends Response
{
    use HasEmbeddedModels;

    public function note(): ?NoteModel
    {
        return $this->single(NoteModel::class);
    }
}
