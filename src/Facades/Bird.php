<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Facades;

use Illuminate\Support\Facades\Facade;
use SpitsOnline\Bird\Bird as BirdClient;
use SpitsOnline\Bird\Testing\BirdFake;

/**
 * @method static \SpitsOnline\Bird\Resources\Contacts contacts()
 * @method static \SpitsOnline\Bird\Data\SentMessage send(\SpitsOnline\Bird\Messages\Message $message, ?string $channelId = null)
 * @method static void assertSent(?callable $callback = null)
 * @method static void assertNotSent(?callable $callback = null)
 * @method static void assertNothingSent()
 *
 * @see BirdClient
 * @see BirdFake
 */
final class Bird extends Facade
{
    public static function fake(): BirdFake
    {
        self::swap($fake = new BirdFake);

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return BirdClient::class;
    }
}
