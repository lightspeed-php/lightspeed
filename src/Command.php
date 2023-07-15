<?php

namespace Lightspeed;

use PHPUnit\TextUI\Command as BaseCommand;
use PHPUnit\TextUI\TestRunner;
use Lightspeed\TestRunner as LightspeedTestRunner;

class Command extends BaseCommand
{
    private API $api;

    public function __construct(API $api)
    {
        $this->api = $api;
    }

    /**
     * @inheritdoc
     */
    protected function createRunner(): TestRunner
    {
        $customRunner = new LightspeedTestRunner($this->api);
        $testRunner = new TestRunner($this->arguments['loader']);
        $testRunner->setCustomRunner($customRunner);

        return $testRunner;
    }
}
