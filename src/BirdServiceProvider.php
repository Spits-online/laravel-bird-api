<?php

namespace Spits\Bird;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BirdServiceProvider extends PackageServiceProvider
{
    /**
     * This class is a Package Service Provider
     *
     * More info: https://github.com/spatie/laravel-package-tools
     */
    #[\Override]
    public function configurePackage(Package $package): void
    {
        $package
            ->name('bird')
            ->hasConfigFile();
    }
}
