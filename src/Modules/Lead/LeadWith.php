<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Lead;

use Manzadey\SaloonAmoCrm\Query\WithField;

enum LeadWith: string implements WithField
{
    case CATALOG_ELEMENTS = 'catalog_elements';

    case IS_PRICE_MODIFIED_BY_ROBOT = 'is_price_modified_by_robot';

    case LOSS_REASON = 'loss_reason';

    case CONTACTS = 'contacts';

    case ONLY_DELETED = 'only_deleted';

    case SOURCE_ID = 'source_id';

    case SOURCE = 'source';
}
