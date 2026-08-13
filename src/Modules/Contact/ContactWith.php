<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Contact;

use Manzadey\SaloonAmoCrm\Query\WithField;

enum ContactWith: string implements WithField
{
    case CATALOG_ELEMENTS = 'catalog_elements';

    case LEADS = 'leads';

    case CUSTOMERS = 'customers';
}
