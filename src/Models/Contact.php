<?php

namespace Spits\Bird\Models;

use Spits\Bird\Enums\IdentifierKey;
use Spits\Bird\Exceptions\InvalidParameterException;

class Contact
{
    private string $displayName = '';

    private array $identifiers = [];

    private array $attributes = [];

    /**
     * Set the display name for the contact.
     */
    public function displayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Set the phone number for the contact.
     *
     * @throws InvalidParameterException
     */
    public function phoneNumber(string $phoneNumber): static
    {
        if (! $this->isValidPhoneNumber($phoneNumber)) {
            throw InvalidParameterException::invalidPhoneNumber($phoneNumber);
        }

        $this->identifiers[] = [
            'key' => 'phonenumber',
            'value' => $phoneNumber,
        ];

        return $this;
    }

    /**
     * Set the email for this contact
     */
    public function emailAdress(string $emailAdress): static
    {
        $this->identifiers[] = [
            'key' => 'emailaddress',
            'value' => $emailAdress,
        ];

        return $this;
    }

    /**
     * Set attributes for this contact
     */
    public function attribute(string $attribute, string $value): static
    {
        $this->attributes = array_merge($this->attributes, [$attribute => $value]);

        return $this;
    }

    /**
     * Get the phone number of the contact
     */
    public function getPhoneNumber(): ?string
    {
        $identifier = collect($this->identifiers)->firstWhere('key', IdentifierKey::PHONE_NUMBER->value);

        return $identifier['value'] ?? null;
    }

    /**
     * Get the email address of the contact
     */
    public function getEmailAddress()
    {
        $identifier = collect($this->identifiers)->firstWhere('key', IdentifierKey::EMAIL_ADDRESS->value);

        return $identifier['value'] ?? null;
    }

    /**
     * Get the display name of the contact
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Get the contact as an array or object
     */
    public function toArray(): array|object
    {
        return [
            'displayName' => $this->displayName,
            'identifiers' => $this->identifiers,

            ...$this->attributes,
        ];
    }

    /**
     *  Validates if the given phone number is capable of sending SMS
     */
    private function isValidPhoneNumber(string $phoneNumber): bool
    {
        $phoneRegex = config('bird.phone_number_regex');

        return ! $phoneRegex || preg_match($phoneRegex, $phoneNumber) === 1;
    }
}
