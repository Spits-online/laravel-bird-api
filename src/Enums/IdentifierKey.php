<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Enums;

enum IdentifierKey: string
{
    case PhoneNumber = 'phonenumber';
    case EmailAddress = 'emailaddress';
}
