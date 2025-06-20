<?php

namespace Spits\Bird\Concerns;

use Illuminate\Notifications\Notification;

interface IsNotificationChannel
{
    /**
     * Get the endpoint needed for performing the send method.
     */
    public function channelEndpoint(): string;

    /**
     * Get the message from the notification class
     */
    public function getMessage(mixed $notifiable, Notification $notification): mixed;

    /**
     * Send the notification
     */
    public function send(mixed $notifiable, Notification $notification): void;
}
