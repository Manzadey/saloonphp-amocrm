<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Responses;

use Manzadey\SaloonAmoCrm\Modules\Lead\LeadModel;
use Manzadey\SaloonAmoCrm\Responses\HasLinksResponse;
use Manzadey\SaloonAmoCrm\Responses\HasPageResponse;
use Saloon\Http\Response;

class LeadListResponse extends Response
{
    use HasPageResponse;
    use HasLinksResponse;

    /**
     * @return array<LeadModel>
     * @throws \JsonException
     */
    public function leads(): array
    {
        return array_map(
            static fn(array $lead): LeadModel => new LeadModel($lead),
            $this->json('_embedded.leads', [])
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

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isEmpty(): bool
    {
        return empty($this->leads());
    }

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }
}