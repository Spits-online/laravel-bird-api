<?php

namespace Spits\Bird\Messages;

use Spits\Bird\Enums\ChannelType;
use Spits\Bird\Support\MessageTemplate;

class WhatsappMessage extends Message
{
    public ChannelType $viaChannel = ChannelType::WHATSAPP;

    public function __construct(
        protected string          $receiver,
        protected ?MessageTemplate $template = null,
        protected array $body = [],
    ) {
        if (is_null($this->template) && empty($this->body)) {
            throw new \InvalidArgumentException('WhastsappMessage requires either a template or body');
        }
    }

    #[\Override]
    public function toArray(): array
    {
        $message['receiver']['contacts'] = [
            [
                'identifierKey' => "phonenumber",
                'identifierValue' => $this->receiver],
        ];

        if ($this->template) {
            $message['template'] = $this->template->toArray();
        }

        if ($this->body) {
            $message['body'] = $this->body;
        }

        return $message;
    }
}
