<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Enum;

enum GrandTypeEnum: string
{
    case AuthCode = 'authorization_code';

    case RefreshToken = 'refresh_token';
}
