<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Exceptions;

use Illuminate\Http\Client\Response;

/**
 * Bird answered with an error. `$status`, `$errorCode` and `$body` hold what it returned.
 */
final class RequestFailed extends BirdException
{
    /**
     * @param  array<array-key, mixed>  $body
     */
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly ?string $errorCode = null,
        public readonly array $body = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public static function fromResponse(Response $response, string $action): self
    {
        $body = is_array($response->json()) ? $response->json() : [];
        $code = $body['code'] ?? null;

        return new self(
            message: "Bird could not {$action} (HTTP {$response->status()}): ".($body['message'] ?? $response->reason()),
            status: $response->status(),
            errorCode: is_string($code) ? $code : null,
            body: $body,
            previous: $response->toException(),
        );
    }
}
