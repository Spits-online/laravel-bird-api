<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Exceptions;

final class InvalidRecipient extends BirdException
{
    public static function missing(string $notification): self
    {
        return new self("No recipient for `{$notification}`. Call `->to()` on the message, or add a `routeNotificationForBird()` method to the notifiable.");
    }
}
