<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Note;

/**
 * Возможные типы примечаний
 *
 * @link https://www.amocrm.ru/developers/content/crm_platform/events-and-notes#notes-params-info
 */
enum NoteTypeEnum: string
{
    case Common = 'common';

    case CallIn = 'call_in';

    case CallOut = 'call_out';

    case ServiceMessage = 'service_message';

    case MessageCashier = 'message_cashier';

    case Geolocation = 'geolocation';

    case SmsIn = 'sms_in';

    case SmsOut = 'sms_out';

    case ExtendedServiceMessage = 'extended_service_message';

    case Attachment = 'attachment';
}
