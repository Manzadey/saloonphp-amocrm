<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Note\NoteModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Saloon\Http\Response;

class NoteCreateResponse extends Response
{
    use HasLinksResponse;
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<NoteModel>
     */
    public function notes(): ModelCollection
    {
        return $this->embedded('notes', NoteModel::class);
    }
}
