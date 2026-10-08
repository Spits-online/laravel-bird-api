<div align="left">
  <a href="https://github.com/Spits-online/laravel-bird">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/Spits-online/laravel-bird/main/art/banner-dark.png">
      <img alt="Laravel Bird by Spits" src="https://raw.githubusercontent.com/Spits-online/laravel-bird/main/art/banner-light.png">
    </picture>
  </a>

<h1>Bird SMS and WhatsApp for Laravel</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spits-online/laravel-bird.svg?style=flat-square)](https://packagist.org/packages/spits-online/laravel-bird)
[![Tests](https://img.shields.io/github/actions/workflow/status/Spits-online/laravel-bird/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Spits-online/laravel-bird/actions/workflows/run-tests.yml)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/Spits-online/laravel-bird/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/Spits-online/laravel-bird/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spits-online/laravel-bird.svg?style=flat-square)](https://packagist.org/packages/spits-online/laravel-bird)

</div>

Notification channels for [Bird](https://bird.com) (formerly MessageBird), plus a small client for the contacts in your workspace.

```php
use Illuminate\Notifications\Notification;
use SpitsOnline\Bird\Channels\SmsChannel;
use SpitsOnline\Bird\Messages\SmsMessage;

class OrderShipped extends Notification
{
    public function via(object $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toBirdSms(object $notifiable): SmsMessage
    {
        return SmsMessage::create('Your order has shipped!');
    }
}
```

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- A Bird workspace with an SMS and/or WhatsApp channel

## Installation

```bash
composer require spits-online/laravel-bird
```

Add your credentials to `.env`. Only the channels you use need an id.

```env
BIRD_ACCESS_KEY=
BIRD_WORKSPACE_ID=
BIRD_SMS_CHANNEL_ID=
BIRD_WHATSAPP_CHANNEL_ID=
```

You'll find these in Bird:

- **Access key:** Settings → Access keys.
- **Workspace id:** Settings → Workspace.
- **Channel ids:** Channels → your SMS or WhatsApp channel → Channel ID.

That's all the configuration most apps need, so you don't have to publish a config file. When you do want to change something, such as [naming your WhatsApp templates](#naming-templates), create `config/bird.php` with **only the keys you change**. It's merged over the [package defaults](config/bird.php) key by key, so everything you leave out keeps its default:

```php
// config/bird.php
return [
    'templates' => [
        'order_shipped' => [
            'project_id' => env('BIRD_ORDER_SHIPPED_TEMPLATE'),
            'locale' => 'nl',
        ],
    ],
];
```

Don't copy keys at their default value. A copied default looks like a deliberate choice, and it stops following the package when the default changes. To see every option, you can publish the full file with `php artisan vendor:publish --tag="bird-config"`. Keep the keys you change and delete the rest.

## Sending notifications

### Choosing the recipient

The channels send to whatever `routeNotificationForBird()` on your notifiable returns: a phone number in international format, or an array of them to send to several people.

```php
class User extends Authenticatable
{
    use Notifiable;

    public function routeNotificationForBird(): string
    {
        return $this->phone_number; // international format: +31612345678
    }
}
```

To send to someone else, set the recipient on the message (`->to('+31612345678')`), or send an [on-demand notification](https://laravel.com/docs/notifications#on-demand-notifications):

```php
Notification::route('bird', '+31612345678')->notify(new OrderShipped);
```

`bird` is the route name both Bird channels read, like `mail` for the mail channel. A phone number is the same for SMS and WhatsApp, so you set it once, and the notification's `via()` still decides which channels send.

### SMS

Add `SmsChannel` to `via()` and return an `SmsMessage` from `toBirdSms()`, as in the example at the top.

### WhatsApp

To start a WhatsApp conversation you need a template approved in Bird Studio. Add `WhatsAppChannel` to `via()` and return a `WhatsAppMessage` from `toBirdWhatsApp()`:

```php
use SpitsOnline\Bird\Channels\WhatsAppChannel;
use SpitsOnline\Bird\Messages\Template;
use SpitsOnline\Bird\Messages\WhatsAppMessage;

public function via(object $notifiable): array
{
    return [WhatsAppChannel::class];
}

public function toBirdWhatsApp(object $notifiable): WhatsAppMessage
{
    return WhatsAppMessage::template(
        Template::create(
            projectId: 'your-project-id',
            parameters: ['name' => $notifiable->first_name],
            locale: 'nl',
        ),
    );
}
```

The template's parameters are the variables you defined in Bird Studio. Each value is sent with a matching type: a boolean as `boolean`, an int or float as `number`, an array as `object`, and everything else as `string`. The version defaults to `latest`; pass `version:` to pin one.

#### Naming templates

To avoid repeating project ids, give your templates a name in your app's `config/bird.php`. Each entry needs a `project_id`. `version` (default `latest`) and `locale` are optional:

```php
// config/bird.php
return [
    'templates' => [
        'order_shipped' => [
            'project_id' => env('BIRD_ORDER_SHIPPED_TEMPLATE'),
            'locale' => 'nl',
        ],
        'appointment_reminder' => [
            'project_id' => env('BIRD_APPOINTMENT_REMINDER_TEMPLATE'),
            // Pin a version instead of using the latest
            'version' => 'a1b2c3d4-…',
            'locale' => 'en',
        ],
    ],
];
```

Then use them by name:

```php
return WhatsAppMessage::template(
    Template::named('order_shipped', ['name' => $notifiable->first_name]),
);
```

Inside WhatsApp's 24-hour service window you can also send any other [message body Bird supports](https://docs.bird.com/api/channels-api/supported-channels/programmable-whatsapp/sending-whatsapp-messages):

```php
WhatsAppMessage::body([
    'type' => 'text',
    'text' => ['text' => 'Thanks, we received your reply.'],
]);
```

### SMS and WhatsApp together

A notification can use both channels. Each one calls its own method:

```php
public function via(object $notifiable): array
{
    return [SmsChannel::class, WhatsAppChannel::class];
}

public function toBirdSms(object $notifiable): SmsMessage
{
    return SmsMessage::create('Your order has shipped!');
}

public function toBirdWhatsApp(object $notifiable): WhatsAppMessage
{
    return WhatsAppMessage::template(
        Template::named('order_shipped', ['name' => $notifiable->first_name]),
    );
}
```

### Sending without a notification

```php
use SpitsOnline\Bird\Facades\Bird;
use SpitsOnline\Bird\Messages\SmsMessage;

$sent = Bird::send(
    SmsMessage::create('Your code is 123456')->to('+31612345678'),
);

$sent->id;     // Bird's message id
$sent->status; // "accepted"
```

Pass a `channelId` to send through a channel other than the configured one: `Bird::send($message, channelId: '…')`.

## Managing contacts

```php
use SpitsOnline\Bird\Facades\Bird;

// Creates the contact, or updates the one with this phone or email
$contact = Bird::contacts()->upsert('+31612345678', [
    'firstName' => 'Jane',
    'lastName' => 'Doe',
]);

$contact = Bird::contacts()->find($contactId);
$contact->displayName;  // "Jane Doe"
$contact->phoneNumber;  // "+31612345678"
$contact->emailAddress; // "jane@example.com" or null
$contact->attributes;   // ['firstName' => 'Jane', ...]

Bird::contacts()->delete($contactId);
```

Contacts are listed a page at a time:

```php
$page = Bird::contacts()->list(limit: 100);

foreach ($page->contacts as $contact) {
    // ...
}

$next = Bird::contacts()->list(
    limit: 100,
    pageToken: $page->nextPageToken, // null on the last page
);
```

## Error handling

Every exception extends `SpitsOnline\Bird\Exceptions\BirdException`:

| Exception | When |
|---|---|
| `RequestFailed` | Bird returned an error. `$status`, `$errorCode` and `$body` hold what Bird sent back. |
| `ConnectionFailed` | Bird couldn't be reached. |
| `MissingConfiguration` | An env value is missing, or `Template::named()` gets a name that isn't in `config/bird.php`. The message says what to set. |
| `InvalidRecipient` | A notification has no recipient. |
| `BirdException` | A notification uses a channel but doesn't have its `toBirdSms()` or `toBirdWhatsApp()` method. |

```php
use SpitsOnline\Bird\Exceptions\RequestFailed;

try {
    $contact = Bird::contacts()->find($contactId);
} catch (RequestFailed $e) {
    if ($e->status === 404) {
        // the contact doesn't exist
    }

    throw $e;
}
```

When a channel throws, Laravel fires its `NotificationFailed` event. For queued notifications, the job is retried as usual.

## Testing your app

`Bird::fake()` records messages instead of sending them:

```php
use SpitsOnline\Bird\Facades\Bird;
use SpitsOnline\Bird\Messages\Message;

Bird::fake();

$user->notify(new OrderShipped);

Bird::assertSent(function (Message $message) use ($user) {
    return $message->recipients()[0]->value === $user->phone_number;
});
Bird::assertNotSent(fn (Message $message) => /* ... */);
Bird::assertNothingSent();
```

Contact calls go through Laravel's HTTP client, so fake them with `Http::fake()`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently. Upgrading from 1.x? See [UPGRADE](UPGRADE.md).

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [SpitsOnline](https://spits.online)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
