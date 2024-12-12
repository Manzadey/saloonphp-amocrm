<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead\Responses;

use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;
use Saloon\Http\Response;

class LeadCustomFieldsListResponse extends Response
{
    /**
     * @return array
     * @throws \JsonException
     */
    public function fields(): array
    {
        return array_map(
            static fn(array $customField): CustomFieldModel => new CustomFieldModel($customField),
            $this->json('_embedded.custom_fields', []),
        );
    }
}