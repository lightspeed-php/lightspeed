<?php

namespace Tests\Feature;

use GuzzleHttp\Client;

trait HasHttpClient
{
    private function client()
    {
        return new Client([
            'base_uri' => $_SERVER['TEST_BASE_URL'],
            'headers' => [
                'User-Agent' => 'testing',
                'Authorization' => 'Bearer ' . $_SERVER['TEST_API_TOKEN'],
                'Accept' => 'application/json',
            ],
        ]);
    }
}
