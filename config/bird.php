<?php

return [
    /*
     * Access key for bird.com API.
     * This key is required for authentication with bird.com services.
     */
    'access_key' => env('BIRD_ACCESS_KEY'),

    /*
     * Workspace ID for bird.com.
     * This ID identifies your workspace in bird.com.
     */
    'workspace_id' => env('BIRD_WORKSPACE_ID'),

    /*
     * Channels configuration for bird.com notifications.
     */
    'channels' => [
        /*
         * SMS channel ID for bird.com notifications.
         * This channel ID is required for sending SMS notifications.
         */
        'sms' => env('BIRD_SMS_CHANNEL_ID'),

        /*
         * WhatsApp channel ID for bird.com notifications.
         * This channel ID is optional. Leave empty if WhatsApp notifications are not used.
         */
        'whatsapp' => env('BIRD_WHATSAPP_CHANNEL_ID'),

        /*
         * Email channel ID for bird.com notifications.
         * This channel ID is optional. Leave empty if email notifications are not used.
         */
        'email' => env('BIRD_EMAIL_CHANNEL_ID')
    ],

    /*
     * Templates for different notification types.
     * These templates are optional and can be left empty if not used.
     */
    'templates' => [
        /*
         * SMS template ID for bird.com notifications.
         * This template ID is optional. Leave empty if SMS templates are not used.
         */
        'sms' => [],

        /*
         * WhatsApp template ID for bird.com notifications.
         * This template ID is optional. Leave empty if WhatsApp templates are not used.
         */
        'whatsapp' => [],
    ],

    /*
     * Regular expression for validating phone numbers.
     * This regex is used to validate phone numbers for SMS and WhatsApp notifications.
     *
     * Default is set to match numbers in the format + followed by 11 digits.
     * To negate the number verification set this to `null`
     */
    'phone_number_regex' => env('BIRD_PHONE_NUMBER_REGEX', '/^\+\d{11}$/'),
];
