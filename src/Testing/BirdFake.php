<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Testing;

use PHPUnit\Framework\Assert as PHPUnit;
use SpitsOnline\Bird\Bird;
use SpitsOnline\Bird\Data\SentMessage;
use SpitsOnline\Bird\Messages\Message;

/**
 * Records messages instead of sending them. Enable it with `Bird::fake()`.
 */
class BirdFake extends Bird
{
    /** @var list<Message> */
    protected array $sent = [];

    public function __construct()
    {
        parent::__construct('fake', 'fake');
    }

    public function send(Message $message, ?string $channelId = null): SentMessage
    {
        $this->sent[] = $message;

        return new SentMessage('fake-'.count($this->sent), 'accepted', []);
    }

    /**
     * @param  (callable(Message): bool)|null  $callback
     */
    public function assertSent(?callable $callback = null): void
    {
        PHPUnit::assertNotEmpty($this->sent($callback), 'The expected Bird message was not sent.');
    }

    /**
     * @param  (callable(Message): bool)|null  $callback
     */
    public function assertNotSent(?callable $callback = null): void
    {
        PHPUnit::assertEmpty($this->sent($callback), 'An unexpected Bird message was sent.');
    }

    public function assertNothingSent(): void
    {
        $this->assertNotSent();
    }

    /**
     * @param  (callable(Message): bool)|null  $callback
     * @return list<Message>
     */
    public function sent(?callable $callback = null): array
    {
        return array_values(array_filter($this->sent, $callback ?? fn () => true));
    }
}
