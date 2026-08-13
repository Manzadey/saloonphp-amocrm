<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class TagCreateResponse extends Response
{
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<TagModel>
     */
    public function tags(): ModelCollection
    {
        return $this->embedded('tags', TagModel::class);
    }
}
