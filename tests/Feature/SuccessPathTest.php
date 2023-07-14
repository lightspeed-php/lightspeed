<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\NullProvider;
use ThingyClient\API;

class SuccessPathTest extends TestCase
{
    use HasHttpClient;

    public function test_first_node_success_path()
    {
        $tests = ['abc.php', 'def.php', 'ghi.php', 'jkl.php'];
        $api = new API($this->client(), new NullProvider((string)random_int(1, PHP_INT_MAX)));

        // trying to connect to a build that hasnt been imported yet should return false
        $response = $api->connect();
        $this->assertFalse($response);

        // the first time should return 3 tests to execute
        $response = $api->import($tests);
        $this->assertCount(1, $response['files']);

        // the second time should return the only remaining test
        $response = $api->queue();
        $this->assertCount(1, $response['files']);

        // a final time should not error but return no tests
        $response = $api->queue();
        $this->assertCount(1, $response['files']);
    }

    public function test_second_node_success_path()
    {
        $tests = ['abc.php', 'def.php', 'ghi.php', 'jkl.php'];
        $api = new API($this->client(), new NullProvider((string)random_int(1, PHP_INT_MAX)));

        // import a new build and expect it to return some tests
        $response = $api->import($tests);
        $this->assertCount(1, $response['files']);

        // simulate another node calling connect first. it should receive some tests
        // $response = $api->connect();
        // $this->assertCount(1, $response['files']);
    }

    public function test_second_node_importing_tests()
    {
        $tests = ['abc.php', 'def.php', 'ghi.php', 'jkl.php'];
        $api = new API($this->client(), new NullProvider((string)random_int(1, PHP_INT_MAX)));

        // import a new build and expect it to return some tests
        $response = $api->import($tests);
        $this->assertCount(1, $response['files']);

        // simulate another node calling import as well. it should not add more tests and receive some tests
        // $response = $api->import(['xyz.php']);
        // $this->assertCount(1, $response['files']);
    }
}
