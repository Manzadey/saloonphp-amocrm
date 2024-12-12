<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\CustomField;

enum CustomFieldTypeEnum: string
{
    case TEXT = 'text';
    case NUMERIC = 'numeric';
    case CHECKBOX = 'checkbox';
    case SELECT = 'select';
    case MULTISELECT = 'multiselect';
    case DATE = 'date';
    case URL = 'url';
    case TEXTAREA = 'textarea';
    case RADIOBUTTON = 'radiobutton';
    case STREET_ADDRESS = 'streetaddress';
    case SMART_ADDRESS = 'smart_address';
    case BIRTHDAY = 'birthday';
    case LEGAL_ENTITY = 'legal_entity';
    case DATE_TIME = 'date_time';
    case PRICE = 'price';
    case CATEGORY = 'category';
    case ITEMS = 'items';
    case TRACKING_DATA = 'tracking_data';
    case LINKED_ENTITY = 'linked_entity';
    case CHAINED_LIST = 'chained_list';
    case MONETARY = 'monetary';
    case FILE = 'file';
    case PAYER = 'payer';
    case SUPPLIER = 'supplier';
}
