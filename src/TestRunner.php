<?php

namespace Lightspeed;

use PHPUnit\Framework\Reorderable;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use RecursiveIteratorIterator;

class TestRunner
{
    /**
     * @var API
     */
    private $api;

    /**
     * @var array<Test|Reorderable>
     */
    private $tests = [];

    public function __construct(API $api)
    {
        $this->api = $api;
    }

    public function run(TestSuite $suite, TestResult $result)
    {
        // build up a map of test names to suites
        foreach (new RecursiveIteratorIterator($suite) as $test) {
            /** @var Reorderable $test */
            $this->tests[$test->sortId()] = $test;
        }

        // try connect to an existing build
        $apiResponse = $this->api->connect();

        if ($apiResponse === false) {
            // no existing build to join. we need to import one
            // no tests left in the queue to run
            $apiResponse = $this->api->import(array_keys($this->tests));
        }

        $this->recursivelyRun($apiResponse['files'], $result);
    }

    private function recursivelyRun(array &$tests, TestResult $result)
    {
        if (count($tests) > 0) {
            foreach ($tests as $selectedTest) {
                $this->tests[$selectedTest]->run($result);
            }

            $apiResponse = $this->api->queue();
            $this->recursivelyRun($apiResponse['files'], $result);
        }
    }
}
