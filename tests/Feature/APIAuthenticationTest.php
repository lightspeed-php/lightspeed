<?php

namespace Tests\Feature;

use Exception;
use PHPUnit\Framework\TestCase;
use Lightspeed\Providers\NullProvider;
use Lightspeed\API;

class APIAuthenticationTest extends TestCase
{
    /**
     * @dataProvider endpointDataProvider
     */
    public function test_invalid_api_token_auth(string $method, $args)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);

        $opts = [
            'url' => $_SERVER['TEST_BASE_URL'],
            'api_token' => 'invalid',
        ];
        $api = new API(new NullProvider((string)random_int(1, PHP_INT_MAX)), $opts);

        $api->{$method}($args);
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
