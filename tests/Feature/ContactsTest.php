<?php

declare(strict_types=1);

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SpitsOnline\Bird\Exceptions\ConnectionFailed;
use SpitsOnline\Bird\Exceptions\MissingConfiguration;
use SpitsOnline\Bird\Exceptions\RequestFailed;
use SpitsOnline\Bird\Facades\Bird;

const CONTACTS = 'https://api.bird.com/workspaces/ws-123/contacts';

it('finds a contact', function () {
    Http::fake([CONTACTS.'/c-1' => Http::response(birdFixture('contact'))]);

    $contact = Bird::contacts()->find('c-1');

    expect($contact)
        ->id->toBe('6f1c2b9e-3d4a-4b5c-9e8f-0a1b2c3d4e5f')
        ->displayName->toBe('Jane Doe')
        ->phoneNumber->toBe('+31612345678')
        ->emailAddress->toBe('jane@example.com')
        ->attributes->toHaveKey('company', 'Acme')
        ->raw->toHaveKey('identifierCount', 2);

    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'AccessKey test-access-key'));
});

it('lists a page of contacts', function () {
    Http::fake([CONTACTS.'*' => Http::response(birdFixture('contacts-page-1'))]);

    $page = Bird::contacts()->list(limit: 2, pageToken: 'abc');

    expect($page->contacts)->toHaveCount(2)
        ->and($page->contacts[1]->emailAddress)->toBe('john@example.com')
        ->and($page->nextPageToken)->toBe('page-2-token');

    Http::assertSent(fn (Request $request) => $request->url() === CONTACTS.'?limit=2&pageToken=abc');
});

it('has no next page token on the last page', function () {
    Http::fake([CONTACTS.'*' => Http::response(birdFixture('contacts-page-2'))]);

    expect(Bird::contacts()->list()->nextPageToken)->toBeNull();
});

it('upserts a contact by phone number or email address', function (string $identifier, string $path) {
    Http::fake([CONTACTS.'/identifiers/*' => Http::response(birdFixture('contact'), 201)]);

    expect(Bird::contacts()->upsert($identifier, ['firstName' => 'Jane'])->displayName)->toBe('Jane Doe');

    Http::assertSent(fn (Request $request) => $request->method() === 'PATCH'
        && $request->url() === CONTACTS.$path
        && $request->data() === ['attributes' => ['firstName' => 'Jane']]);
})->with([
    'phone' => ['+31612345678', '/identifiers/phonenumber/%2B31612345678'],
    'email' => ['jane@example.com', '/identifiers/emailaddress/jane%40example.com'],
]);

it('deletes a contact', function () {
    Http::fake([CONTACTS.'/c-1' => Http::response(status: 204)]);

    Bird::contacts()->delete('c-1');

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});

it('throws with Bird\'s status, code and body when a request fails', function () {
    Http::fake([CONTACTS.'/missing' => Http::response(['code' => 'NotFound', 'message' => 'Contact not found'], 404)]);

    try {
        Bird::contacts()->find('missing');
        $this->fail('Expected RequestFailed');
    } catch (RequestFailed $e) {
        expect($e->getMessage())->toBe('Bird could not find contact `missing` (HTTP 404): Contact not found')
            ->and($e->status)->toBe(404)
            ->and($e->errorCode)->toBe('NotFound')
            ->and($e->body)->toHaveKey('message');
    }
});

it('wraps connection failures', function () {
    Http::fake(fn () => throw new ConnectionException('timed out'));

    Bird::contacts()->find('c-1');
})->throws(ConnectionFailed::class, 'Could not connect to the Bird API: timed out');

it('names the env key when credentials are missing', function () {
    config()->set('bird.access_key', null);
    app()->forgetInstance(SpitsOnline\Bird\Bird::class);
    Bird::clearResolvedInstances();

    Bird::contacts()->find('c-1');
})->throws(MissingConfiguration::class, 'Add `BIRD_ACCESS_KEY` to your .env file.');
