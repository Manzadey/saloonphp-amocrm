<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Responses;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Saloon\Http\Response;

class LeadItemResponse extends Response
{
    public function lead(): ?LeadModel
    {
        $data = $this->json();

        if (empty($data)) {
            return null;
        }

        return new LeadModel($data);
    }
}