<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SpitsOnline\Bird\Tests\TestCase;

uses(TestCase::class)->in('Feature');

// A test must never reach the real Bird API.
beforeEach(fn () => Http::preventStrayRequests());

/**
 * @return array<string, mixed>
 */
function birdFixture(string $name): array
{
    return json_decode((string) file_get_contents(__DIR__."/Fixtures/{$name}.json"), true, flags: JSON_THROW_ON_ERROR);
}
