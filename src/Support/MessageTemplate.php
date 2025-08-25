<?php

namespace Spits\Bird\Support;

class MessageTemplate
{
    public function __construct(
        protected string $projectId,
        protected string $version,
        protected string $locale,
        protected ?array $attachments = null,
        protected ?array $shortLinks = null,
        protected ?array $variables = null,
        protected ?array $utmParameters = null,
        protected ?array $parameters = null,
        protected ?array $settings = null,

    )
    {

    }

    public function toArray(): array
    {
        return [
            'projectId'     => $this->projectId,
            'version'       => $this->version,
            'locale'        => $this->locale,
            'attachments'   => $this->attachments,
            'shortLinks'    => $this->shortLinks,
            'variables'     => $this->variables,
            'utmParameters' => $this->utmParameters,
            'parameters'    => $this->parameters,
            'settings'      => $this->settings,
        ];
    }

//    public function projectId(string $projectId): static
//    {
//        $this->projectId = $projectId;
//
//        return $this;
//    }
//
//    public function version(string $version): static
//    {
//        $this->version = $version;
//
//        return $this;
//    }
//
//    public function locale(string $locale): static
//    {
//        $this->locale = $locale;
//
//        return $this;
//    }
//
//    public function parameters(array $parameters): static
//    {
//        $this->parameters = $parameters;
//
//        return $this;
//    }
//
//    public function getProjectId(): string
//    {
//        return $this->projectId;
//    }
//
//    public function getVersion(): string
//    {
//        return $this->version;
//    }
//
//    public function getLocale(): string
//    {
//        return $this->locale;
//    }
//
//    public function getParameters(): array
//    {
//        return $this->parameters;
//    }
}
