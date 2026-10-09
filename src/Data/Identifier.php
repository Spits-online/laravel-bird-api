<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Data;

use SpitsOnline\Bird\Enums\IdentifierKey;

/**
 * How Bird identifies a contact: a phone number or an email address.
 */
final readonly class Identifier
{
    public function __construct(
        public IdentifierKey $key,
        public string $value,
    ) {}

    public static function phone(string $phoneNumber): self
    {
        return new self(IdentifierKey::PHONE_NUMBER, $phoneNumber);
    }

    public static function email(string $emailAddress): self
    {
        return new self(IdentifierKey::EMAIL_ADDRESS, $emailAddress);
    }

    /**
     * A phone number or email address, detected from the value.
     */
    public static function from(self|string $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return str_contains($value, '@') ? self::email($value) : self::phone($value);
    }
}
