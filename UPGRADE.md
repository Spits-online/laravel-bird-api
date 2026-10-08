# Upgrade guide

## From 1.x to 2.0

Version 2 is a rewrite. Most changes are renames you can do with search and replace. Version 1 is no longer supported and won't get bug or security fixes.

### Checklist

- [ ] You're on PHP 8.3+ and Laravel 12 or 13
- [ ] Switch to the new package name: `composer remove spits-online/laravel-bird-api`, then `composer require spits-online/laravel-bird:^2.0`
- [ ] Replace `Spits\Bird\` with `SpitsOnline\Bird\` across your app
- [ ] Rename the classes and methods below
- [ ] If you published `config/bird.php`, shrink it to the keys you actually change. The rest now comes from the package, merged key by key
- [ ] Replace `ContactService` calls
- [ ] Replace `catch` blocks and code that checked for `[]` / `false` return values

### Renamed classes

| 1.x | 2.0 |
|---|---|
| `Spits\Bird\Channels\SMSChannel` | `SpitsOnline\Bird\Channels\SmsChannel` |
| `Spits\Bird\Channels\WhatsappChannel` | `SpitsOnline\Bird\Channels\WhatsAppChannel` |
| `Spits\Bird\Messages\SMSMessage` | `SpitsOnline\Bird\Messages\SmsMessage` |
| `Spits\Bird\Messages\WhatsappMessage` | `SpitsOnline\Bird\Messages\WhatsAppMessage` |
| `Spits\Bird\Support\MessageTemplate` | `SpitsOnline\Bird\Messages\Template` |
| `Spits\Bird\Services\ContactService` | `SpitsOnline\Bird\Facades\Bird::contacts()` |
| `Spits\Bird\Exceptions\NotificationNotSent` | `SpitsOnline\Bird\Exceptions\RequestFailed` |
| `Spits\Bird\Exceptions\InvalidParameterException` | `SpitsOnline\Bird\Exceptions\MissingConfiguration` |

The email channel was never implemented and has been removed.

### Notifications

The message methods have the channel name in them, so they can't clash with other SMS packages. Recipients come from `routeNotificationForBird()`.

Before:

```php
public function toSMS($notifiable): SMSMessage
{
    return (new SMSMessage())
        ->text('Your order has shipped!')
        ->toContact((new Contact())->phoneNumber($notifiable->phone_number));
}
```

After:

```php
public function toBirdSms(object $notifiable): SmsMessage
{
    return SmsMessage::create('Your order has shipped!');
}

// on the notifiable
public function routeNotificationForBird(): string
{
    return $this->phone_number;
}
```

`->to($phoneNumber)` on the message still works when you want to set the recipient in the notification.

### WhatsApp templates

`MessageTemplate` becomes `Template`, and the receiver moves from the constructor to `->to()` or `routeNotificationForBird()`. Bird replaced template `variables` with typed `parameters`. Pass the same key/value pairs and the types are filled in for you.

Before:

```php
public function toWhatsapp($notifiable): WhatsappMessage
{
    return new WhatsappMessage(
        receiver: $notifiable->phone_number,
        template: new MessageTemplate(
            projectId: config('bird.templates.whatsapp.foo_template.template_project_id'),
            version: config('bird.templates.whatsapp.foo_template.template_version'),
            locale: config('bird.templates.whatsapp.foo_template.template_locale'),
            variables: ['receiverFirstName' => $notifiable->first_name],
        ),
    );
}
```

After:

```php
public function toBirdWhatsApp(object $notifiable): WhatsAppMessage
{
    return WhatsAppMessage::template(
        Template::named('foo_template', [
            'receiverFirstName' => $notifiable->first_name,
        ]),
    );
}
```

The config entry loses its `whatsapp` level and the `template_` prefixes:

```php
'templates' => [
    'foo_template' => [
        'project_id' => '…',
        'locale' => 'nl',
    ],
],
```

Classes that extended `MessageTemplate` to set defaults can be replaced with `Template::named()`.

`new WhatsappMessage(receiver: …, body: [...])` becomes `WhatsAppMessage::body([...])->to(…)`.

### Contacts

Methods return typed objects and throw `RequestFailed` or `ConnectionFailed` instead of returning `[]`, `false` or an error array.

| 1.x | 2.0 |
|---|---|
| `(new ContactService)->index(limit: 20)` → array | `Bird::contacts()->list(limit: 20)` → `ContactPage` (`->contacts`, `->nextPageToken`) |
| `(new ContactService)->show($id)` → array or `[]` | `Bird::contacts()->find($id)` → `Contact` |
| `(new ContactService)->createOrUpdate($contact, IdentifierKey::PHONE_NUMBER)` → array or `[]` | `Bird::contacts()->upsert('+31612345678', ['firstName' => 'Jane'])` → `Contact` |
| `(new ContactService)->delete($id)` → `true` or array | `Bird::contacts()->delete($id)` → `void` |

`list()` no longer has a `reverse` option. The `Contact` builder (`->displayName()->phoneNumber()->attribute()`) is gone: pass the identifier and an attributes array to `upsert()`. Bird has deprecated `displayName` as a field; set `firstName` / `lastName` attributes and read `$contact->displayName`.

### Configuration

- An app's `config/bird.php` now only needs the keys it changes. It is merged over the package defaults recursively, so setting `channels.sms` alone keeps the `whatsapp` channel.
- `bird.channels.email` and `bird.phone_number_regex` are gone. Numbers must be in international format (`+31612345678`); Bird validates them.
- `bird.templates` holds named templates directly (see above).

### Error handling

Nothing is logged by the package any more. Exceptions are thrown, and all of them extend `SpitsOnline\Bird\Exceptions\BirdException`.
