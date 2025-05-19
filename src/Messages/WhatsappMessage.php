<?php

namespace Spits\Bird\Messages;

use Spits\Bird\Enums\ChannelType;

class WhatsappMessage extends Message
{
    public ChannelType $viaChannel = ChannelType::WHATSAPP;

    public function __construct(protected string $templateProjectId, protected string $templateVersion, protected string $templateLocale,protected string $receiver, protected array $templateVariables = [])
    {

    }

    #[\Override]
    public function toArray(): array
    {
        $message['receiver']['contacts'] =[
            ['identifierValue'=>$this->receiver]
        ] ;
        $message['template'] = [
            'projectId' => $this->templateProjectId,
            'version' => $this->templateVersion,
            'locale' => $this->templateLocale,
            'variables' => $this->templateVariables,
        ];

        return $message;
    }
}
