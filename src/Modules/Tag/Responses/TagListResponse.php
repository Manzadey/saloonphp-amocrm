<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Tag\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Tag\TagModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class TagListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<TagModel>
     */
    public function tags(): ModelCollection
    {
        return $this->embedded('tags', TagModel::class);
    }
}
