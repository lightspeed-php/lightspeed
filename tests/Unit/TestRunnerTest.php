<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use Lightspeed\API;
use Lightspeed\TestRunner;

class TestRunnerTest extends TestCase
{
    public function test_existing_empty_build_doesnt_run_any_tests()
    {
        $api = $this->createMock(API::class);
        // the api client should try connect only once
        $api->expects($this->once())->method('connect')->willReturn(['files' => []]);

        $runner =  new TestRunner($api);
        $suite = $this->getMockBuilder(TestSuite::class)
            ->enableProxyingToOriginalMethods()
            ->onlyMethods(['run'])
            ->getMock();
        // the test suite should never be run because the api responded with no tests
        $suite->expects($this->never())->method('run');
        $suite->setTests([]);

        $runner->run($suite, new TestResult);
    }

    public function test_existing_build_runs_test_until_empty()
    {
        $api = $this->createMock(API::class);
        // the api client should try connect only once
        $api->expects($this->once())->method('connect')->willReturn(['files' => ['test 1']]);
        $api->expects($this->exactly(2))->method('queue')->willReturn(['files' => ['test 2']], ['files' => []]);

        $suite = new TestSuite();
        $testCase = fn ($name) => new class($name) extends TestCase {
            public function sortId(): string {
                return $this->getName();
            }
        };
        $suite->addTest($testCase('test 1'));
        $suite->addTest($testCase('test 2'));
        $this->assertCount(2, $suite->tests());

        $runner =  new TestRunner($api);

        $runner->run($suite, new TestResult);
    }

    public function test_new_empty_build_doesnt_run_any_tests()
    {
        $api = $this->createMock(API::class);
        // the api client should try connect only once
        $api->expects($this->once())->method('connect')->willReturn(false);
        // and import only once but return no files
        $api->expects($this->once())->method('import')->willReturn(['files' => []]);

        $runner =  new TestRunner($api);
        $suite = $this->getMockBuilder(TestSuite::class)
            ->enableProxyingToOriginalMethods()
            ->onlyMethods(['run'])
            ->getMock();
        // the test suite should never be run because the api responded with no tests
        $suite->expects($this->never())->method('run');
        $suite->setTests([]);

        $runner->run($suite, new TestResult);
    }

    public function test_new_build_runs_returned_tests_until_empty()
    {
        $api = $this->createMock(API::class);
        // the api client should try connect only once
        $api->expects($this->once())->method('connect')->willReturn(false);
        // and call import only once with some tests
        $api->expects($this->once())->method('import')->willReturn(['files' => ['test 1', 'test 2']]);
        // and call queue twice returning some tests once and none the next time
        $api->expects($this->exactly(2))->method('queue')->willReturn(['files' => ['test 3']], ['files' => []]);

        $suite = new TestSuite();
        $testCase = fn ($name) => new class($name) extends TestCase {
            public function sortId(): string {
                return $this->getName();
            }
        };
        $suite->addTest($testCase('test 1'));
        $suite->addTest($testCase('test 2'));
        $suite->addTest($testCase('test 3'));
        $this->assertCount(3, $suite->tests());

        $runner =  new TestRunner($api);

        $runner->run($suite, new TestResult);
    }

    public function test_dependency_order_works_correctly()
    {
        $this->markTestSkipped('Need to implement a test to confirm the @depends annotation works');
    }

    public function test_dataprovider_works_correctly()
    {
        $this->markTestSkipped('Need to implement a test to confirm the @dataProvider annotation works');
    }
}
