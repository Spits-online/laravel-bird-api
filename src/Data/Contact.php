<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Data;

/**
 * A contact as Bird returns it. `$raw` holds the full payload.
 */
final readonly class Contact
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $id,
        public ?string $displayName,
        public ?string $phoneNumber,
        public ?string $emailAddress,
        public array $attributes,
        public array $raw,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $identifiers = collect((array) ($payload['featuredIdentifiers'] ?? []))->pluck('value', 'key');
        $attributes = $payload['attributes'] ?? [];

        return new self(
            id: (string) ($payload['id'] ?? ''),
            displayName: isset($payload['computedDisplayName']) ? (string) $payload['computedDisplayName'] : null,
            phoneNumber: $identifiers->get('phonenumber'),
            emailAddress: $identifiers->get('emailaddress'),
            attributes: is_array($attributes) ? $attributes : [],
            raw: $payload,
        );
    }
}
