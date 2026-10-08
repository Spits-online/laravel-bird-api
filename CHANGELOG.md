# Changelog

All notable changes to `laravel-bird-api` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-10-08

### Added
- `Bird::fake()` with `assertSent()`, `assertNotSent()` and `assertNothingSent()` for testing apps.
- `Bird::send()` to send a message without a notification, returning a `SentMessage` with Bird's message id.
- Typed `Contact` and `ContactPage` objects for contact responses.
- `Template::named()` to build a template from `config('bird.templates')`.
- WhatsApp template parameters (`['name' => 'Jane']`), Bird's current replacement for `variables`.
- Recipients can be phone numbers or email addresses, and a message can have several.
- Exceptions that say what went wrong: `RequestFailed` (with Bird's `$status`, `$errorCode` and `$body`), `ConnectionFailed`, `MissingConfiguration` (names the env key to set) and `InvalidRecipient`, all extending `BirdException`.
- An app's `config/bird.php` only needs the keys it changes. It is merged over the defaults key by key instead of one level deep.

### Changed
- **Breaking:** the namespace is now `SpitsOnline\Bird` instead of `Spits\Bird`.
- **Breaking:** requires PHP 8.3+ and Laravel 12 or 13.
- **Breaking:** notifications implement `toBirdSms()` / `toBirdWhatsApp()` instead of `toSMS()` / `toWhatsapp()`, and notifiables route with `routeNotificationForBird()`.
- **Breaking:** `SMSChannel`, `SMSMessage`, `WhatsappChannel` and `WhatsappMessage` are renamed to `SmsChannel`, `SmsMessage`, `WhatsAppChannel` and `WhatsAppMessage`.
- **Breaking:** `ContactService` is replaced by `Bird::contacts()` with `list()`, `find()`, `upsert()` and `delete()`. These return typed objects and throw on failure instead of returning arrays, `[]` or `false`.
- **Breaking:** `MessageTemplate` is replaced by `Template`.
- **Breaking:** requests authenticate with `Authorization: AccessKey …`, the format Bird documents for access keys.
- Contacts are updated through `attributes`. Bird has deprecated the `identifiers` and `displayName` fields the 1.x client sent.

### Removed
- **Breaking:** the unfinished email channel, and the empty `Channel` model and `HasBirdMessage` trait.
- **Breaking:** the `Contact` builder and `IdentifierKey` constants used by `ContactService`.
- **Breaking:** the `bird.phone_number_regex` config option. Bird validates numbers itself, and the default pattern rejected valid numbers.
- **Breaking:** exception logging. Exceptions are thrown, and your app decides what to log.

### Fixed
- `ContactService::delete()` crashed on connection errors and returned an array from a `bool` method.
- Connection errors in `show()` and `createOrUpdate()` were logged and swallowed, returning `[]`.

## [1.1.1] - 2026-04-21

### Added
- Laravel 13 support.

## [1.1.0] - 2025-08-25

### Added
- `MessageTemplate` class to help set up messages that use templates created in Bird.

### Changed
- The `WhatsappMessage` class now expects either a `MessageTemplate` or a `body` parameter to be set.

## [1.0.0] - 2025-05-19

- Initial release.

[Unreleased]: https://github.com/Spits-online/laravel-bird-api/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/Spits-online/laravel-bird-api/compare/V1.1.1...v2.0.0
[1.1.1]: https://github.com/Spits-online/laravel-bird-api/compare/V1.1.0...V1.1.1
[1.1.0]: https://github.com/Spits-online/laravel-bird-api/compare/V1.0.0...V1.1.0
[1.0.0]: https://github.com/Spits-online/laravel-bird-api/releases/tag/V1.0.0
