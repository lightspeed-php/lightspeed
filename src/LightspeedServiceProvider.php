<?php

namespace Lightspeed;

use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use Lightspeed\Providers\ProviderFactory;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as BaseTestCommand;

class LightspeedServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // override resolution of the artistan 'test' command to our version
        $this->app->bind(BaseTestCommand::class, TestCommand::class);
    }

    public function register()
    {
        $provider = ProviderFactory::resolveProvider();

        ParallelTesting::resolveTokenUsing(function () use ($provider) {
            if ($provider !== false) {
                return $provider->nodeIndex() + 1;
            }
            return 'lightspeed';
        });
    }
}
