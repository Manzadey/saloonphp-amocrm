<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField\Responses;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\CustomField\CustomFieldModel;
use Manzadey\SaloonAmoCrm\Responses\HasEmbeddedModels;
use Saloon\Http\Response;

class CustomFieldsListResponse extends Response
{
    use HasEmbeddedModels;

    /**
     * @return ModelCollection<CustomFieldModel>
     */
    public function fields(): ModelCollection
    {
        return $this->embedded('custom_fields', CustomFieldModel::class);
    }
}
