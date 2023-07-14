<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\NullProvider;
use ThingyClient\API;

class APIAuthenticationTest extends TestCase
{
    /**
     * @dataProvider endpointDataProvider
     */
    public function test_invalid_api_token_auth(string $method, $args)
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(401);

        $client = new Client([
            'base_uri' => $_SERVER['TEST_BASE_URL'],
            'headers' => [
                'User-Agent' => 'testing',
                'Authorization' => 'Bearer invalid',
                'Accept' => 'application/json',
            ],
        ]);
        $api = new API($client, new NullProvider((string)random_int(1, PHP_INT_MAX)));

        $api->{$method}($args);
    }

    public function test_another()
    {
        $this->assertTrue(true);
    }

    public function endpointDataProvider()
    {
        return [
            'connect' => ['connect', null],
            'import' => ['import', []],
            'queue' => ['queue', null],
        ];
    }
}
