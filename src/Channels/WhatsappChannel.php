<?php

namespace Spits\Bird\Channels;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use Spits\Bird\Concerns\IsNotificationChannel;
use Spits\Bird\Contracts\BirdConnection;
use Spits\Bird\Exceptions\InvalidParameterException;
use Spits\Bird\Exceptions\NotificationNotSent;
use Spits\Bird\Messages\WhatsappMessage;

class WhatsappChannel implements IsNotificationChannel
{
    use BirdConnection;

    /**
     * @throws InvalidParameterException
     */
    public function channelEndpoint(): string
    {
        $channelID = config('bird.channels.whatsapp');

        if (! $channelID) {
            throw InvalidParameterException::configValueIsNotSet('bird.channels.whatsapp');
        }

        return $this->endpoint("channels/$channelID/messages");
    }

    public function getMessage(mixed $notifiable, Notification $notification): mixed
    {
        if (! method_exists($notification, 'toWhatsapp')) {
            throw new \InvalidArgumentException('Notification does not implement toWhatsapp method');
        }

        return $notification->toWhatsapp($notifiable, $notification);
    }

    /**
     * Send the notification
     *
     * @throws InvalidParameterException
     * @throws NotificationNotSent
     * @throws ConnectionException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        /** @var WhatsappMessage $message */
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
