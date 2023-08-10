<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use Lightspeed\Providers\NullProvider;
use Lightspeed\TestRunner;

class TestRunnerTest extends TestCase
{
    public function test_no_tests()
    {
        $runner =  new TestRunner(new NullProvider(1, 0));
        $suite = $this->getMockBuilder(TestSuite::class)
            ->enableProxyingToOriginalMethods()
            ->onlyMethods(['run'])
            ->getMock();
        // the test suite should never be run because the api responded with no tests
        $suite->expects($this->never())->method('run');
        $suite->setTests([]);

        $result = new TestResult();
        $runner->run($suite, $result);
        // but only one was run
        $this->assertCount(0, $result);
    }

    /**
     * @dataProvider subsetOfTests
     */
    public function test_runs_subset_of_all_tests($totalTests, $nodeCount, $nodeIndex, $runTests)
    {
        $suite = new TestSuite();
        // add all tests to the suite
        for ($i = 0; $i < $totalTests; $i++) {
            $suite->addTest($this->newTestCaseClass("test $i"));
        }
        $this->assertCount($totalTests, $suite->tests());

        $result = new TestResult();
        $runner =  new TestRunner(new NullProvider($nodeCount, $nodeIndex));
        $runner->run($suite, $result);

        // assert all tests were run
        $this->assertCount($runTests, $result);
    }

    public function subsetOfTests()
    {
        return [
            // format: [total number of tests, node count, node index, expected number of run tests]
            'node pair index 0' => [2, 2, 0, 1],
            'node pair index 1' => [2, 2, 1, 1],
            'odd index 0' => [3, 2, 1, 1],
            'large even' => [148, 40, 0, 4],
            'large odd' => [347, 7, 0, 50],
            'large odd last index' => [347, 7, 6, 47],
        ];
    }

    /**
     * @dataProvider numberOfTests
     */
    public function test_all_tests_are_run($totalTests, $nodeCount)
    {
        $suite = new TestSuite();
        // add all tests to the suite
        for ($i = 0; $i < $totalTests; $i++) {
            $suite->addTest($this->newTestCaseClass("test $i"));
        }
        $this->assertCount($totalTests, $suite->tests());

        $result = new TestResult();
        // create separate test runners to mock separate nodes
        for ($i = 0; $i < $nodeCount; $i++) {
            $runner =  new TestRunner(new NullProvider($nodeCount, $i));
            $runner->run($suite, $result);
        }

        // assert all tests were run
        $this->assertCount($totalTests, $result);
    }

    public function numberOfTests()
    {
        return [
            // format: [total number of tests, node count]
            'even' => [2, 2],
            'odd' => [3, 2],
            'large even' => [148, 40],
            'large odd' => [347, 7],
        ];
    }

    private function newTestCaseClass($name)
    {
        return new class($name) extends TestCase {
            public function sortId(): string {
                return $this->getName();
            }
        };
    }
}
