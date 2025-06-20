<?php

namespace Spits\Bird\Messages;

use Spits\Bird\Contracts\HasBirdMessage;
use Spits\Bird\Enums\ChannelType;
use Spits\Bird\Enums\IdentifierKey;
use Spits\Bird\Enums\MessageType;
use Spits\Bird\Models\Contact;

abstract class Message
{
    use HasBirdMessage;

    public ChannelType $viaChannel;

    public MessageType $messageType;

    public array $contacts = [];

    public array $actions = [];

    public string $text;

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function addContact(Contact $contact, IdentifierKey $identifierKey): static
    {
        $this->contacts[] = [
            'identifierKey' => $identifierKey->value,
            'identifierValue' => $identifierKey === IdentifierKey::PHONE_NUMBER
                ? $contact->getPhoneNumber()
                : $contact->getEmailAddress(),
        ];

        return $this;
    }

    public function addAction(string $action, array $parameters = []): static
    {
        $this->actions[] = [
            'action' => $action,
            'parameters' => $parameters,
        ];

        return $this;
    }

    public function toArray(): array
    {
        return [
            'body' => [
                'type' => $this->messageType->value,
                $this->messageType->value => [
                    'text' => $this->text,
                ],
            ],
            'receiver' => [
                'contacts' => $this->contacts,
            ],
        ];
    }
}
