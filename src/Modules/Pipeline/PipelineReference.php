<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline;

use Manzadey\SaloonAmoCrm\Connectors\MainConnector;

class PipelineReference
{
    public function __construct(
        protected readonly MainConnector $connector
    ) {
    }

    public function list(): Requests\PipelineListRequest
    {
        return new Requests\PipelineListRequest($this->connector);
    }
}
