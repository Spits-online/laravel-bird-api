<?php

declare(strict_types=1);

namespace SpitsOnline\Bird;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use SpitsOnline\Bird\Data\SentMessage;
use SpitsOnline\Bird\Exceptions\ConnectionFailed;
use SpitsOnline\Bird\Exceptions\MissingConfiguration;
use SpitsOnline\Bird\Exceptions\RequestFailed;
use SpitsOnline\Bird\Messages\Message;
use SpitsOnline\Bird\Resources\Contacts;

class Bird
{
    /**
     * @param  array<string, string|null>  $channels  message channel => Bird channel id
     */
    public function __construct(
        protected ?string $accessKey,
        protected ?string $workspaceId,
        protected array $channels = [],
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromConfig(array $config): self
    {
        return new self(
            accessKey: $config['access_key'] ?? null,
            workspaceId: $config['workspace_id'] ?? null,
            channels: $config['channels'] ?? [],
        );
    }

    public function contacts(): Contacts
    {
        return new Contacts($this);
    }

    /**
     * Send a message through the channel configured for its type, or through `$channelId`.
     */
    public function send(Message $message, ?string $channelId = null): SentMessage
    {
        $channelId ??= $this->channels[$message->channel()]
            ?? throw MissingConfiguration::key("channels.{$message->channel()}", 'BIRD_'.strtoupper($message->channel()).'_CHANNEL_ID');

        $response = $this->request('post', "channels/{$channelId}/messages", $message->toArray(), 'send the message');

        return SentMessage::fromArray($response->json() ?? []);
    }

    /**
     * Request builder that is sent to Bird.com
     *
     * @param  'get'|'post'|'patch'|'delete'  $method
     * @param  array<string, mixed>  $data
     *
     * @internal
     */
    public function request(string $method, string $path, array $data = [], string $action = 'complete the request'): Response
    {
        $accessKey = $this->accessKey ?: throw MissingConfiguration::key('access_key', 'BIRD_ACCESS_KEY');
        $workspaceId = $this->workspaceId ?: throw MissingConfiguration::key('workspace_id', 'BIRD_WORKSPACE_ID');

        try {
            $response = Http::baseUrl("https://api.bird.com/workspaces/{$workspaceId}/")
                ->withHeaders(['Authorization' => "AccessKey {$accessKey}"])
                ->acceptJson()
                ->{$method}($path, $data === [] ? null : $data);
        } catch (ConnectionException $e) {
            throw ConnectionFailed::from($e);
        }

        if ($response->failed()) {
            throw RequestFailed::fromResponse($response, $action);
        }

        return $response;
    }
}
