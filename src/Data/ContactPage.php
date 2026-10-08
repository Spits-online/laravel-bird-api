<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Data;

/**
 * One page of contacts. `$nextPageToken` is null on the last page.
 */
final readonly class ContactPage
{
    /**
     * @param  list<Contact>  $contacts
     */
    public function __construct(
        public array $contacts,
        public ?string $nextPageToken,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            contacts: array_values(array_map(Contact::fromArray(...), (array) ($payload['results'] ?? []))),
            nextPageToken: ($payload['nextPageToken'] ?? null) ?: null,
        );
    }
}
