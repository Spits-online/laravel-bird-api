<?php

namespace Spits\Bird\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Spits\Bird\Concerns\IsNotificationChannel;
use Spits\Bird\Contracts\BirdConnection;
use Spits\Bird\Exceptions\NotificationNotSent;

class EmailChannel implements IsNotificationChannel
{
    use BirdConnection;

    public function channelEndpoint(): string
    {
        return '';
    }

    public function getMessage($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toBirdEmail')) {
            throw new \InvalidArgumentException('Notification does not implement toBirdEmail method');
        }

        return $notification->toBirdEmail($notification);
    }

    public function send(mixed $notifiable, Notification $notification): void
    {
        $message = $this->getMessage($notification);

        $response = $this->birdRequest($this->channelEndpoint(), $message->toArray());

        if (! $response->accepted()) {
            throw NotificationNotSent::notificationType(
                notification: get_class($notification),
                status: $response->status(),
                errorMessage: $response->json()
            );
        }
    }
}
