<?php

namespace Lightspeed\Providers;

class ProviderFactory
{
    const PROVIDERS = [
        BuildkiteProvider::class,
    ];

    public static function resolveProvider()
    {
        foreach (self::PROVIDERS as $create) {
            $provider = new $create;
            if ($provider->detect()) {
                return $provider;
            }
        }

        return false;
    }
}
