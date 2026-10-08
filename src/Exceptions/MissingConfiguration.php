<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Exceptions;

final class MissingConfiguration extends BirdException
{
    public static function key(string $key, string $env): self
    {
        return new self("The `bird.{$key}` config value is not set. Add `{$env}` to your .env file.");
    }
}
