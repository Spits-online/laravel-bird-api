<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | The access key (Bird → Settings → Access keys) and the workspace it
    | belongs to (Bird → Settings → Workspace). Both are read from the
    | environment, so they never end up in the repository.
    |
    */

    'access_key' => env('BIRD_ACCESS_KEY'),
    'workspace_id' => env('BIRD_WORKSPACE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Channels
    |--------------------------------------------------------------------------
    |
    | The Bird channel each message type is sent through (Bird → Channels →
    | [channel] → Channel ID). Only the channels the app uses need a value;
    | sending through an empty one throws and names the env key to set.
    |
    */

    'channels' => [
        'sms' => env('BIRD_SMS_CHANNEL_ID'),
        'whatsapp' => env('BIRD_WHATSAPP_CHANNEL_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Named Bird Studio templates, so notifications can send
    | `Template::named('order_shipped', [...])` instead of repeating project
    | ids. Each entry takes a `project_id`, and optionally a `version`
    | (default `latest`) and `locale`. Empty here: templates belong to the app.
    |
    | See https://github.com/Spits-online/laravel-bird#naming-templates
    |
    */

    'templates' => [],

];
