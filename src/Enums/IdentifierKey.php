<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Enums;

enum IdentifierKey: string
{
    case PHONE_NUMBER = 'phonenumber';
    case EMAIL_ADDRESS = 'emailaddress';
}
