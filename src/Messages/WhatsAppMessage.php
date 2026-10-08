<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Messages;

/**
 * Build one with `WhatsAppMessage::template()`, or `WhatsAppMessage::body()` for any
 * other body Bird supports inside WhatsApp's 24-hour service window.
 */
class WhatsAppMessage extends Message
{
    /**
     * @param  array<string, mixed>  $body
     */
    final protected function __construct(
        protected ?Template $template = null,
        protected array $body = [],
    ) {}

    public static function template(Template $template): static
    {
        return new static(template: $template);
    }

    /**
     * @param  array<string, mixed>  $body  e.g. `['type' => 'text', 'text' => ['text' => 'Thanks!']]`
     */
    public static function body(array $body): static
    {
        return new static(body: $body);
    }

    public function channel(): string
    {
        return 'whatsapp';
    }

    protected function content(): array
    {
        return $this->template ? ['template' => $this->template->toArray()] : ['body' => $this->body];
    }
}
