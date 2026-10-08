<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Exceptions;

use Illuminate\Http\Client\ConnectionException;

final class ConnectionFailed extends BirdException
{
    public static function from(ConnectionException $exception): self
    {
        return new self("Could not connect to the Bird API: {$exception->getMessage()}", previous: $exception);
    }
}
