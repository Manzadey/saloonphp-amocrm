<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline\Requests;

use Manzadey\SaloonAmoCrm\Modules\Pipeline\Responses\PipelineListResponse;
use Manzadey\SaloonAmoCrm\Requests\SendsTypedResponse;
use Saloon\Enums\Method;

/**
 * @see https://www.amocrm.ru/developers/content/crm_platform/leads_pipelines
 */
class PipelineListRequest extends AbstractPipelineRequest
{
    use SendsTypedResponse;

    protected Method $method = Method::GET;

    protected ?string $response = PipelineListResponse::class;

    /**
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function send(): PipelineListResponse
    {
        return $this->sendTyped(PipelineListResponse::class);
    }
}
