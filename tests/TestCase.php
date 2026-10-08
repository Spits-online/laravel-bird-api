<?php

declare(strict_types=1);

namespace SpitsOnline\Bird\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SpitsOnline\Bird\BirdServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [BirdServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('bird.access_key', 'test-access-key');
        $app['config']->set('bird.workspace_id', 'ws-123');
        $app['config']->set('bird.channels.sms', 'sms-channel');
        $app['config']->set('bird.channels.whatsapp', 'wa-channel');
    }
}
