<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\AssertionFailedError;
use SpitsOnline\Bird\Facades\Bird;
use SpitsOnline\Bird\Messages\Message;
use SpitsOnline\Bird\Messages\SmsMessage;

it('records messages instead of sending them', function () {
    Bird::fake();

    Bird::send(SmsMessage::create('Hi')->to('+31612345678'));

    Http::assertNothingSent();
    Bird::assertSent(fn (Message $message) => $message->recipients()[0]->value === '+31612345678');
});

it('fails when the expected message was not sent', function () {
    Bird::fake();

    Bird::assertNothingSent();
    expect(fn () => Bird::assertSent())->toThrow(AssertionFailedError::class);
});
