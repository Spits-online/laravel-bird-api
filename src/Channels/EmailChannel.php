<?php

namespace Spits\Bird\Channels;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use Spits\Bird\Concerns\IsNotificationChannel;
use Spits\Bird\Contracts\BirdConnection;
use Spits\Bird\Exceptions\InvalidParameterException;
use Spits\Bird\Exceptions\NotificationNotSent;

class EmailChannel implements IsNotificationChannel
{
    use BirdConnection;

    public function channelEndpoint(): string
    {
        return '';
    }

    public function getMessage($notifiable, Notification $notification): mixed
    {
        if (! method_exists($notification, 'toBirdEmail')) {
            throw new \InvalidArgumentException('Notification does not implement toBirdEmail method');
        }

        return $notification->toBirdEmail($notification);
    }

    /**
     * @throws InvalidParameterException
     * @throws NotificationNotSent
     * @throws ConnectionException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        /** @var Notification $notification */
        $message = $this->getMessage($notification, $notification);

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
