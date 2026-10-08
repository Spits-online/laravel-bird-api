<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Data;

/**
 * Bird's receipt for a message it accepted. `$raw` holds the full payload.
 */
final readonly class SentMessage
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public string $id,
        public string $status,
        public array $raw,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            id: (string) ($payload['id'] ?? ''),
            status: (string) ($payload['status'] ?? ''),
            raw: $payload,
        );
    }
}
