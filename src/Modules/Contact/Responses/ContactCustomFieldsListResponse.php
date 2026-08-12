<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact\Responses;

use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;
use Saloon\Http\Response;

class ContactCustomFieldsListResponse extends Response
{
    /**
     * @return array<CustomFieldModel>
     * @throws \JsonException
     */
    public function fields(): array
    {
        return array_map(
            static fn (array $customField): CustomFieldModel => new CustomFieldModel($customField),
            $this->json('_embedded.custom_fields', []),
        );
    }
}
