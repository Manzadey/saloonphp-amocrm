<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Pipeline\PipelineModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class PipelineListResponse extends Response
{
    use HasEmbeddedModels;

    /**
     * amoCRM отдаёт 204 No Content вместо пустого списка, когда воронок нет —
     * `ModelCollection::of()` превращает такое тело в пустую коллекцию.
     *
     * @return ModelCollection<PipelineModel>
     */
    public function pipelines(): ModelCollection
    {
        return $this->embedded('pipelines', PipelineModel::class);
    }
}
