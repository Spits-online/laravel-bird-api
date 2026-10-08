<?php

declare(strict_types=1);

namespace SpitsOnline\Bird;

use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class BirdServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('bird')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->mergeConfigRecursively();

        $this->app->singleton(Bird::class, fn (Application $app) => Bird::fromConfig((array) $app->make('config')->get('bird', [])));
    }

    /**
     * Laravel merges a package config one level deep, so an app that sets only
     * `channels.sms` would lose `channels.whatsapp`. Merging the package file
     * underneath again, key by key, lets the app's `config/bird.php` state only
     * what differs. The config cache already holds the merged result.
     */
    private function mergeConfigRecursively(): void
    {
        if ($this->app->configurationIsCached()) {
            return;
        }

        $config = $this->app->make('config');

        $config->set('bird', self::merge(require __DIR__.'/../config/bird.php', (array) $config->get('bird', [])));
    }

    /**
     * Associative arrays merge recursively, lists are replaced wholesale: an app
     * that lists two entries means those two.
     *
     * @param  array<array-key, mixed>  $base
     * @param  array<array-key, mixed>  $override
     * @return array<array-key, mixed>
     */
    private static function merge(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            $base[$key] = is_array($value)
                && is_array($base[$key] ?? null)
                && ! array_is_list($value)
                && ! array_is_list($base[$key])
                    ? self::merge($base[$key], $value)
                    : $value;
        }

        return $base;
    }
}
