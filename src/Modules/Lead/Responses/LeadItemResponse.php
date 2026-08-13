<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Responses;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class LeadItemResponse extends Response
{
    use HasEmbeddedModels;

    public function lead(): ?LeadModel
    {
        return $this->single(LeadModel::class);
    }
}
