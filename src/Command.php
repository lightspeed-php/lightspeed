<?php

namespace ThingyClient;

use PHPUnit\TextUI\Command as BaseCommand;
use PHPUnit\TextUI\TestRunner;
use ThingyClient\TestRunner as ThingyClientTestRunner;

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
        $customRunner = new ThingyClientTestRunner($this->api);
        $testRunner = new TestRunner($this->arguments['loader']);
        $testRunner->setCustomRunner($customRunner);

        return $testRunner;
    }
}
