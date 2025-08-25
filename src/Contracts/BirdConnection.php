<?php

namespace Spits\Bird\Contracts;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Spits\Bird\Exceptions\InvalidParameterException;

trait BirdConnection
{
    /**
     * Generates the headers required for API requests to the Bird platform.
     *
     * @return array The headers for Bird API
     *
     * @throws InvalidParameterException
     */
    protected function headers(): array
    {
        $accessKey = config('bird.access_key');

        if (! $accessKey) {
            throw InvalidParameterException::configValueIsNotSet('bird.access_key');
        }

        return [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessKey",
        ];
    }

    /**
     * This method generates the API endpoint URL for the Bird platform. \
     * It retrieves the workspace ID from the application's configuration and throws an exception if it's not set.
     *
     * @throws InvalidParameterException
     */
    protected function endpoint(?string $path = null): string
    {
        $apiEndpoint = 'https://api.bird.com';
        $workspaceID = config('bird.workspace_id');

        if (! $workspaceID) {
            throw InvalidParameterException::configValueIsNotSet('bird.workspace_id');
        }

        $endpoint = "$apiEndpoint/workspaces/$workspaceID";

        return $path
            ? "$endpoint/$path"
            : $endpoint;
    }

    /**
     * Make the post-request to Bird.com to create the message.
     *
     * @throws InvalidParameterException
     * @throws ConnectionException
     */
    protected function birdRequest(string $url, ?array $params = null, string $method = 'post'): PromiseInterface|Response
    {
        return Http::withHeaders($this->headers())->{$method}($url, $params);
    }
}
