<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Messages;

class SmsMessage extends Message
{
    final public function __construct(
        protected string $text,
    ) {}

    public static function create(string $text): static
    {
        return new static($text);
    }

    public function channel(): string
    {
        return 'sms';
    }

    protected function content(): array
    {
        return [
            'body' => [
                'type' => 'text',
                'text' => ['text' => $this->text],
            ],
        ];
    }
}
