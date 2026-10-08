<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Channels;

use Illuminate\Notifications\Notification;
use SpitsOnline\Bird\Bird;
use SpitsOnline\Bird\Data\SentMessage;
use SpitsOnline\Bird\Exceptions\BirdException;
use SpitsOnline\Bird\Exceptions\InvalidRecipient;
use SpitsOnline\Bird\Messages\Message;

abstract class Channel
{
    /**
     * The notification method that builds the message, e.g. `toBirdSms`.
     */
    protected const string METHOD = '';

    public function __construct(
        protected Bird $bird,
    ) {}

    public function send(object $notifiable, Notification $notification): SentMessage
    {
        $message = method_exists($notification, static::METHOD)
            ? $notification->{static::METHOD}($notifiable)
            : null;

        if (! $message instanceof Message) {
            throw new BirdException('`'.$notification::class.'` needs a `'.static::METHOD.'()` method that returns a Bird message.');
        }

        if (! $message->hasRecipients()) {
            $route = method_exists($notifiable, 'routeNotificationFor')
                ? $notifiable->routeNotificationFor('bird', $notification)
                : null;

            $message->to(...(array) ($route ?: throw InvalidRecipient::missing($notification::class)));
        }

        return $this->bird->send($message);
    }
}
