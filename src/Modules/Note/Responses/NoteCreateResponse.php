<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Responses;

use Manzadey\SaloonAmoCrm\Modules\Note\NoteModel;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Saloon\Http\Response;

class NoteCreateResponse extends Response
{
    use HasLinksResponse;

    public function notes(): array
    {
        return array_map(
            static fn(array $note): NoteModel => new NoteModel($note),
            $this->json('_embedded.notes', [])
        );
    }
}