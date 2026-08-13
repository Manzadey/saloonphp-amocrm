<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline\Responses;

use Manzadey\SaloonAmoCrm\Modules\Pipeline\PipelineModel;
use Saloon\Http\Response;

class PipelineListResponse extends Response
{
    /**
     * amoCRM returns 204 No Content instead of an empty list when the
     * account has no pipelines; Saloon's json() already falls back to an
     * empty array for an empty body, so no extra 204 handling is needed here.
     *
     * @return array<PipelineModel>
     * @throws \JsonException
     */
    public function pipelines(): array
    {
        return array_map(
            static fn (array $pipeline): PipelineModel => new PipelineModel($pipeline),
            $this->json('_embedded.pipelines', [])
        );
    }

    /**
     * @return bool
     * @throws \JsonException
     */
    public function isEmpty(): bool
    {
        return empty($this->pipelines());
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
