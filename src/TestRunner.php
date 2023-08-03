<?php

namespace Lightspeed;

use Lightspeed\Providers\Provider;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use RecursiveIteratorIterator;

class TestRunner
{
    /**
     * @var Provider
     */
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function run(TestSuite $suite, TestResult $result)
    {
        // pull out all tests into an array
        $tests = [];
        $testCount = 0;
        foreach (new RecursiveIteratorIterator($suite) as $test) {
            $tests[] = $test;
            $testCount++;
        }

        $sliceLength = ceil($testCount / $this->provider->nodeCount());
        $offset = $sliceLength * $this->provider->nodeIndex();
        $run = array_slice($tests, $offset, $sliceLength);

        foreach ($run as $test) {
            $result = $test->run($result);
        }
    }
}
