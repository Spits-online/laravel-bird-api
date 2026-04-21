<?php

it('loads the package config', function () {
    expect(config('bird'))->toBeArray()
        ->and(config('bird'))->toHaveKey('access_key')
        ->and(config('bird'))->toHaveKey('workspace_id');
});

it('registers the service provider', function () {
    expect(app()->getLoadedProviders())
        ->toHaveKey(\Spits\Bird\BirdServiceProvider::class);
});
