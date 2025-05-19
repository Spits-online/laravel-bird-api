<?php

namespace Spits\Bird\Exceptions;

use Exception;

class InvalidParameterException extends Exception
{
    public static function configValueIsNotSet(string $configKey): self
    {
        return new self("The given config value `{$configKey}` is not set or is empty.");
    }

    public static function invalidPhoneNumber(string $phoneNumber): self
    {
        return new self("The given phone number `{$phoneNumber}` does not appear to be valid.");
    }
}
