<?php

namespace Spits\Bird\Notifications\Channels;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Spits\Bird\Concerns\IsNotificationChannel;
use Spits\Bird\Contracts\BirdConnection;
use Spits\Bird\Exceptions\InvalidParameterException;
use Spits\Bird\Exceptions\NotificationNotSent;
use Spits\Bird\Messages\SMSMessage;

class SMSChannel implements IsNotificationChannel
{
    use BirdConnection;

    /**
     * @throws InvalidParameterException
     */
    public function channelEndpoint(): string
    {
        $channelID = config('bird.channels.sms');

        if (! $channelID) {
            throw InvalidParameterException::configValueIsNotSet('bird.channels.sms');
        }

        return $this->endpoint("channels/$channelID/messages");
    }

    public function getMessage(mixed $notifiable, Notification $notification): mixed
    {
        if (! method_exists($notification, 'toSMS')) {
            throw new \InvalidArgumentException('Notification does not implement toSMS method');
        }

        return $notification->toSMS($notifiable, $notification);
    }

    /**
     * Send the notification
     *
     * @throws ConnectionException
     * @throws NotificationNotSent
     * @throws InvalidParameterException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        /** @var SMSMessage $message */
        $message = $this->getMessage($notifiable, $notification);

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
