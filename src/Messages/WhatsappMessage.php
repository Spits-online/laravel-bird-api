<?php

namespace Spits\Bird\Messages;

use Spits\Bird\Enums\ChannelType;
use Spits\Bird\Models\Template;

class WhatsappMessage extends Message
{
    public ChannelType $viaChannel = ChannelType::WHATSAPP;

    public function __construct(
        protected Template $template,
        protected string $receiver
    ) {}

    #[\Override]
    public function toArray(): array
    {
        $message['receiver']['contacts'] = [
            ['identifierValue' => $this->receiver],
        ];

        $message['template'] = [
            'projectId' => $this->template->getProjectId(),
            'version'   => $this->template->getVersion(),
            'variables' => $this->template->getParameters(),
        ];

        return $message;
    }
}
