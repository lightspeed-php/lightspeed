<?php

namespace Lightspeed;

use PHPUnit\TextUI\Command as BaseCommand;
use PHPUnit\TextUI\TestRunner;
use Lightspeed\TestRunner as LightspeedTestRunner;

class Command extends BaseCommand
{
    /** @var LightspeedTestRunner */
    private $runner;

    public function __construct(LightspeedTestRunner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @inheritdoc
     */
    protected function createRunner(): TestRunner
    {
        $testRunner = new TestRunner($this->arguments['loader']);
        $testRunner->setCustomRunner($this->runner);

        return $testRunner;
    }
}
