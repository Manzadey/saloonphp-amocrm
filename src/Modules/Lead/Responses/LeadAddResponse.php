<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Responses;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Saloon\Http\Response;

class LeadAddResponse extends Response
{
    /**
     * @return array<LeadModel>
     * @throws \JsonException
     */
    public function leads(): array
    {
        return array_map(
            static fn(array $item) => new LeadModel($item),
            $this->json('_embedded.leads', []),
        );
    }

    /**
     * @return LeadModel|null
     * @throws \JsonException
     */
    public function lead(): ?LeadModel
    {
        return $this->leads()[0] ?? null;
    }
}