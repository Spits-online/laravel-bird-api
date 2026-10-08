<?php

declare(strict_types=1);

use SpitsOnline\Bird\BirdServiceProvider;

it('merges an app config over the defaults key by key', function () {
    // What an app's config/bird.php with only these keys leaves in the repository.
    config()->set('bird', [
        'channels' => ['sms' => 'app-sms-channel'],
        'templates' => ['order_shipped' => ['project_id' => 'proj']],
    ]);

    (new BirdServiceProvider(app()))->register();

    expect(config('bird.channels'))->toHaveKeys(['sms', 'whatsapp'])
        ->and(config('bird.channels.sms'))->toBe('app-sms-channel')
        ->and(config('bird.templates.order_shipped.project_id'))->toBe('proj')
        ->and(config('bird'))->toHaveKeys(['access_key', 'workspace_id']);
});

it('replaces lists instead of appending to them', function () {
    config()->set('bird', ['templates' => ['order_shipped' => ['project_id' => 'proj', 'tags' => ['a']]]]);

    (new BirdServiceProvider(app()))->register();

    expect(config('bird.templates.order_shipped.tags'))->toBe(['a']);
});
