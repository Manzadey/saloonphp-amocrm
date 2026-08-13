<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class LeadListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<LeadModel>
     */
    public function leads(): ModelCollection
    {
        return $this->embedded('leads', LeadModel::class);
    }
}
