<?php

namespace Spits\Bird\Messages;

use Spits\Bird\Concerns\IsBirdMessage;
use Spits\Bird\Enums\ChannelType;
use Spits\Bird\Enums\IdentifierKey;
use Spits\Bird\Enums\MessageType;
use Spits\Bird\Models\Contact;

class SMSMessage extends Message implements IsBirdMessage
{
    public ChannelType $viaChannel = ChannelType::SMS;
    public MessageType $messageType = MessageType::TEXT;

    public function toContact(Contact $contact): static
    {
        $this->addContact($contact, IdentifierKey::PHONE_NUMBER);

        return $this;
    }
}
