<?php

namespace Spits\Bird\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spits\Bird\Contracts\BirdConnection;
use Illuminate\Http\Client\Response;
use Spits\Bird\Enums\IdentifierKey;
use Spits\Bird\Exceptions\InvalidParameterException;
use Spits\Bird\Models\Contact;

class ContactService
{
    use BirdConnection;

    /**
     * Retrieve contacts.
     *
     * @param int $limit The number of contacts to retrieve. Default is 10.
     * @param bool $reverse Whether to retrieve contacts in reverse order. Default is false.
     * @param string $nextPageToken The token for the next page of results. Default is an empty string.
     * @return array The retrieved contacts, or null on failure.
     * @throws InvalidParameterException|ConnectionException
     */
    public function index(
        int     $limit = 10,
        bool    $reverse = false,
        string  $nextPageToken = ''
    ): array {
        $endpoint = $this->endpoint('contacts');

        $query = [
            'limit'         => $limit,
            'reverse'       => $reverse,
            'nextPageToken' => $nextPageToken,
        ];

        return $this->birdRequest($endpoint, $query, 'get')->json();
    }

    /**
     * Get a contact by ID.
     *
     * @param string $contactId
     * @return array
     * @throws InvalidParameterException
     */
    public function show(string $contactId): array
    {
        $endpoint = $this->endpoint("contacts/$contactId");

        try {
            $res = $this->birdRequest($endpoint, null, 'get')->json();
        } catch (ConnectionException $e) {
            Log::info($e->getMessage());

            $res = [];
        }

        return $res;
    }

    /**
     * Creates or updates a contact.
     *
     * @param Contact $contact The contact you are creating or updating.
     * @param IdentifierKey $identifierKey How to identify the contact in Bird.
     * @return array The response from the API, or null on failure.
     * @throws InvalidParameterException
     */
    public function createOrUpdate(Contact $contact, IdentifierKey $identifierKey): array
    {
        $endpoint = $this->getIdentifyEndpoint($contact, $identifierKey);

        try {
            $res = $this->birdRequest($endpoint, $contact->toArray(), 'patch')->json();
        } catch (ConnectionException $e) {
            Log::info($e->getMessage());

            $res = [];
        }

        return $res;
    }

    /**
     * Delete a contact in Bird.
     *
     * @param string $contactId
     * @return bool The response from the API, or true if deletion was successful.
     * @throws InvalidParameterException
     */
    public function delete(string $contactId): bool
    {
        $endpoint = $this->endpoint("contacts/$contactId");

        try {
            $res = $this->birdRequest($endpoint, null, 'delete');
        } catch (ConnectionException $e) {
            $res = false;
        }

        if ($res->status() !== 204) {
            return $res->json();
        }

        return true;
    }

    /**
     * Get the contact's unique identifier route.
     *
     * @throws InvalidParameterException
     */
    private function getIdentifyEndpoint(Contact $contact, IdentifierKey $identifierKey): string
    {
        $identifierValue =  match ($identifierKey) {
            IdentifierKey::PHONE_NUMBER  => $contact->getPhoneNumber(),
            IdentifierKey::EMAIL_ADDRESS => $contact->getEmailAddress(),
        };

        return $this->endpoint("contacts/identifiers/{$identifierKey->value}/{$identifierValue}");
    }
}
