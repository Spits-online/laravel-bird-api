<?php

namespace Spits\Bird\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class NotificationNotSent extends Exception
{
    public static function notificationType($notification, $status, $errorMessage): self
    {
        Log::error($errorMessage);

        return new self("Could not send `$notification` notification. Returned with status `$status`.");
    }
}
