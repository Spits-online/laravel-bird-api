<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Channels;

/**
 * Sends the notification's `toBirdWhatsApp()` message.
 */
class WhatsAppChannel extends Channel
{
    protected const string METHOD = 'toBirdWhatsApp';
}
