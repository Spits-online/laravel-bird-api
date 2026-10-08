<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Resources;

use SpitsOnline\Bird\Bird;
use SpitsOnline\Bird\Data\Contact;
use SpitsOnline\Bird\Data\ContactPage;
use SpitsOnline\Bird\Data\Identifier;

class Contacts
{
    public function __construct(
        protected Bird $bird,
    ) {}

    /**
     * One page of contacts. Pass the page's `nextPageToken` to get the next one.
     */
    public function list(int $limit = 25, ?string $pageToken = null): ContactPage
    {
        $query = array_filter(['limit' => $limit, 'pageToken' => $pageToken]);

        return ContactPage::fromArray($this->bird->request('get', 'contacts', $query, 'list the contacts')->json() ?? []);
    }

    public function find(string $contactId): Contact
    {
        return Contact::fromArray($this->bird->request('get', 'contacts/'.rawurlencode($contactId), action: "find contact `{$contactId}`")->json() ?? []);
    }

    /**
     * Create the contact with this phone number or email address, or update it if it exists.
     *
     * @param  array<string, mixed>  $attributes  e.g. `['firstName' => 'Jane', 'lastName' => 'Doe']`
     */
    public function upsert(Identifier|string $identifier, array $attributes = []): Contact
    {
        $identifier = Identifier::from($identifier);
        $path = 'contacts/identifiers/'.$identifier->key->value.'/'.rawurlencode($identifier->value);

        $response = $this->bird->request('patch', $path, ['attributes' => $attributes], "save contact `{$identifier->value}`");

        return Contact::fromArray($response->json() ?? []);
    }

    public function delete(string $contactId): void
    {
        $this->bird->request('delete', 'contacts/'.rawurlencode($contactId), action: "delete contact `{$contactId}`");
    }
}
