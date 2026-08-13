<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class LeadAddResponse extends Response
{
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<LeadModel>
     */
    public function leads(): ModelCollection
    {
        return $this->embedded('leads', LeadModel::class);
    }
}
