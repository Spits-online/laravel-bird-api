<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Messages;

use SpitsOnline\Bird\Exceptions\MissingConfiguration;

/**
 * A message template created in Bird Studio.
 */
final readonly class Template
{
    /**
     * @param  array<string, mixed>  $parameters  the template's variables, e.g. `['name' => 'Jane']`
     */
    public function __construct(
        public string $projectId,
        public string $version = 'latest',
        public ?string $locale = null,
        public array $parameters = [],
    ) {}

    /**
     * @param  array<string, mixed>  $parameters  the template's variables, e.g. `['name' => 'Jane']`
     */
    public static function create(string $projectId, array $parameters = [], ?string $locale = null, string $version = 'latest'): self
    {
        return new self($projectId, $version, $locale, $parameters);
    }

    /**
     * A template from `config('bird.templates.{name}')`.
     *
     * @param  array<string, mixed>  $parameters
     */
    public static function named(string $name, array $parameters = []): self
    {
        $config = config("bird.templates.{$name}");

        if (! is_array($config) || empty($config['project_id'])) {
            throw MissingConfiguration::key("templates.{$name}.project_id", 'the template project id');
        }

        return new self($config['project_id'], $config['version'] ?? 'latest', $config['locale'] ?? null, $parameters);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'projectId' => $this->projectId,
            'version' => $this->version,
            'locale' => $this->locale,
            'parameters' => array_map(fn (string $key, mixed $value) => [
                'type' => match (true) {
                    is_bool($value) => 'boolean',
                    is_int($value), is_float($value) => 'number',
                    is_array($value) => 'object',
                    default => 'string',
                },
                'key' => $key,
                'value' => $value,
            ], array_keys($this->parameters), $this->parameters),
        ]);
    }
}
