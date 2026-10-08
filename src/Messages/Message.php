<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Messages;

use SpitsOnline\Bird\Data\Identifier;

abstract class Message
{
    /** @var list<Identifier> */
    protected array $recipients = [];

    /**
     * The `bird.channels` key this message is sent through.
     */
    abstract public function channel(): string;

    /**
     * The `body` or `template` part of the request.
     *
     * @return array<string, mixed>
     */
    abstract protected function content(): array;

    /**
     * Add recipients: phone numbers, email addresses or Identifiers.
     */
    public function to(Identifier|string ...$recipients): static
    {
        foreach ($recipients as $recipient) {
            $this->recipients[] = Identifier::from($recipient);
        }

        return $this;
    }

    /**
     * @return list<Identifier>
     */
    public function recipients(): array
    {
        return $this->recipients;
    }

    public function hasRecipients(): bool
    {
        return $this->recipients !== [];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'receiver' => [
                'contacts' => array_map(fn (Identifier $recipient) => [
                    'identifierKey' => $recipient->key->value,
                    'identifierValue' => $recipient->value,
                ], $this->recipients),
            ],
            ...$this->content(),
        ];
    }
}
