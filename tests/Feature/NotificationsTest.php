<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification as Notifications;
use SpitsOnline\Bird\Channels\SmsChannel;
use SpitsOnline\Bird\Channels\WhatsAppChannel;
use SpitsOnline\Bird\Exceptions\BirdException;
use SpitsOnline\Bird\Exceptions\InvalidRecipient;
use SpitsOnline\Bird\Messages\SmsMessage;
use SpitsOnline\Bird\Messages\Template;
use SpitsOnline\Bird\Messages\WhatsAppMessage;

class OrderShipped extends Notification
{
    public function __construct(public ?string $to = null) {}

    public function via(object $notifiable): array
    {
        return [SmsChannel::class, WhatsAppChannel::class];
    }

    public function toBirdSms(object $notifiable): SmsMessage
    {
        $message = SmsMessage::create('Your order has shipped');

        return $this->to ? $message->to($this->to) : $message;
    }

    public function toBirdWhatsApp(object $notifiable): WhatsAppMessage
    {
        return WhatsAppMessage::template(new Template('project-1'));
    }
}

class Customer
{
    use Notifiable;

    public function routeNotificationForBird(): string
    {
        return '+31612345678';
    }
}

beforeEach(fn () => Http::fake(['api.bird.com/*' => Http::response(birdFixture('message-accepted'), 202)]));

function sentTo(string $channel): ?string
{
    $request = Http::recorded(fn (Request $request) => str_contains($request->url(), "/channels/{$channel}/"))->first()[0] ?? null;

    return $request['receiver']['contacts'][0]['identifierValue'] ?? null;
}

it('sends each channel to the notifiable', function () {
    (new Customer)->notify(new OrderShipped);

    expect(sentTo('sms-channel'))->toBe('+31612345678')
        ->and(sentTo('wa-channel'))->toBe('+31612345678');
});

it('keeps the recipient set on the message', function () {
    (new Customer)->notify(new OrderShipped(to: '+31699999999'));

    expect(sentTo('sms-channel'))->toBe('+31699999999');
});

it('supports on-demand notifications', function () {
    Notifications::route('bird', '+31611111111')->notify(new OrderShipped);

    expect(sentTo('sms-channel'))->toBe('+31611111111');
});

it('explains a missing recipient', function () {
    (new AnonymousNotifiable)->notify(new OrderShipped);
})->throws(InvalidRecipient::class, 'No recipient for `OrderShipped`');

it('explains a notification without a Bird message method', function () {
    $notification = new class extends Notification
    {
        public function via(object $notifiable): array
        {
            return [SmsChannel::class];
        }
    };

    (new Customer)->notify($notification);
})->throws(BirdException::class, 'needs a `toBirdSms()` method that returns a Bird message');
