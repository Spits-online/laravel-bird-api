<?php

namespace Spits\Bird\Models;

class Template
{
    private string $projectId;

    private string $version;

    private string $locale;

    private array $parameters = [];

    public function projectId(string $projectId): static
    {
        $this->projectId = $projectId;

        return $this;
    }

    public function version(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function locale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function parameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
