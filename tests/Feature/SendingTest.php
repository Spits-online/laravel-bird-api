<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SpitsOnline\Bird\Bird as BirdClient;
use SpitsOnline\Bird\Exceptions\MissingConfiguration;
use SpitsOnline\Bird\Exceptions\RequestFailed;
use SpitsOnline\Bird\Facades\Bird;
use SpitsOnline\Bird\Messages\SmsMessage;
use SpitsOnline\Bird\Messages\Template;
use SpitsOnline\Bird\Messages\WhatsAppMessage;

const MESSAGES = 'https://api.bird.com/workspaces/ws-123/channels';

beforeEach(fn () => Http::fake([MESSAGES.'/*' => Http::response(birdFixture('message-accepted'), 202)]));

it('sends an SMS', function () {
    $sent = Bird::send(SmsMessage::create('Your order has shipped')->to('+31612345678'));

    expect($sent)->id->toBe('e2b8c1a4-5f6d-4e7a-8b9c-0d1e2f3a4b5c')->status->toBe('accepted');

    Http::assertSent(fn (Request $request) => $request->url() === MESSAGES.'/sms-channel/messages'
        && $request->data() === [
            'receiver' => ['contacts' => [['identifierKey' => 'phonenumber', 'identifierValue' => '+31612345678']]],
            'body' => ['type' => 'text', 'text' => ['text' => 'Your order has shipped']],
        ]);
});

it('sends a WhatsApp template', function () {
    Bird::send(WhatsAppMessage::template(new Template('project-1', 'v3', 'nl', ['name' => 'Jane', 'items' => 3]))->to('+31612345678'));

    Http::assertSent(fn (Request $request) => $request->url() === MESSAGES.'/wa-channel/messages'
        && $request['template'] === [
            'projectId' => 'project-1',
            'version' => 'v3',
            'locale' => 'nl',
            'parameters' => [
                ['type' => 'string', 'key' => 'name', 'value' => 'Jane'],
                ['type' => 'number', 'key' => 'items', 'value' => 3],
            ],
        ]);
});

it('sends a WhatsApp body', function () {
    Bird::send(WhatsAppMessage::body(['type' => 'text', 'text' => ['text' => 'Thanks!']])->to('+31612345678'));

    Http::assertSent(fn (Request $request) => $request['body'] === ['type' => 'text', 'text' => ['text' => 'Thanks!']]);
});

it('sends to email addresses and several recipients', function () {
    Bird::send(SmsMessage::create('Hi')->to('+31612345678', 'jane@example.com'));

    Http::assertSent(fn (Request $request) => $request['receiver']['contacts'][1] === ['identifierKey' => 'emailaddress', 'identifierValue' => 'jane@example.com']);
});

it('sends through an explicit channel', function () {
    Bird::send(SmsMessage::create('Hi')->to('+31612345678'), channelId: 'other');

    Http::assertSent(fn (Request $request) => $request->url() === MESSAGES.'/other/messages');
});

it('builds a template from config', function () {
    config()->set('bird.templates.order_shipped', ['project_id' => 'proj', 'locale' => 'nl']);

    expect(Template::named('order_shipped', ['vip' => true])->toArray())->toBe([
        'projectId' => 'proj',
        'version' => 'latest',
        'locale' => 'nl',
        'parameters' => [['type' => 'boolean', 'key' => 'vip', 'value' => true]],
    ]);
});

it('creates a template with named arguments', function () {
    expect(Template::create('proj', ['name' => 'Jane'], locale: 'nl')->toArray())->toBe([
        'projectId' => 'proj',
        'version' => 'latest',
        'locale' => 'nl',
        'parameters' => [['type' => 'string', 'key' => 'name', 'value' => 'Jane']],
    ]);
});

it('explains a missing template', function () {
    Template::named('nope');
})->throws(MissingConfiguration::class, 'bird.templates.nope.project_id');

it('names the env key when a channel is not configured', function () {
    config()->set('bird.channels.sms', null);
    app()->forgetInstance(BirdClient::class);
    Bird::clearResolvedInstances();

    Bird::send(SmsMessage::create('Hi')->to('+31612345678'));
})->throws(MissingConfiguration::class, 'Add `BIRD_SMS_CHANNEL_ID` to your .env file.');

it('throws when Bird rejects the message', function () {
    Http::swap(new Factory);
    Http::preventStrayRequests();
    Http::fake([MESSAGES.'/*' => Http::response(birdFixture('error-malformed'), 422)]);

    Bird::send(SmsMessage::create('Hi')->to('+31612345678'));
})->throws(RequestFailed::class, 'One or more fields provided in the request body are malformed');

it('sends through another workspace with a client from fromConfig(), leaving the facade on the configured one', function () {
    Http::fake(['api.bird.com/*' => Http::response(birdFixture('message-accepted'), 202)]);

    $other = BirdClient::fromConfig([
        'access_key' => 'other-key',
        'workspace_id' => 'other-workspace',
        'channels' => ['sms' => 'other-sms-channel'],
    ]);
    $other->send(SmsMessage::create('Hi!')->to('+31612345678'));
    Bird::send(SmsMessage::create('Hi!')->to('+31612345678'));

    Http::assertSent(fn (Request $request) => $request->url() === 'https://api.bird.com/workspaces/other-workspace/channels/other-sms-channel/messages'
        && $request->header('Authorization') === ['AccessKey other-key']);
    Http::assertSent(fn (Request $request) => $request->url() === MESSAGES.'/sms-channel/messages');
});
