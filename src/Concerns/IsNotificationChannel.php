<?php

namespace Spits\Bird\Concerns;

use Illuminate\Notifications\Notification;

interface IsNotificationChannel
{
    /**
     * Get the endpoint needed for performing the send method.
     *
     * @return string
     */
    public function channelEndpoint(): string;

    /**
     * Get the message from the notification class
     *
     * @param Notification $notification
     * @return mixed
     */
    public function getMessage(mixed $notifiable, Notification $notification);

    /**
     * Send the notification
     *
     * @param mixed $notifiable
     * @param Notification $notification
     */
    public function send(mixed $notifiable, Notification $notification): void;
}
