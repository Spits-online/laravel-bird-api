<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Channels;

/**
 * Sends the notification's `toBirdSms()` message.
 */
class SmsChannel extends Channel
{
    protected const string METHOD = 'toBirdSms';
}
