<?php

namespace Lightspeed\Providers;

use Lightspeed\Providers\Provider;

class NullProvider implements Provider
{
    public $nodeCount;
    public $nodeIndex;

    public function __construct(int $nodeCount, int $nodeIndex)
    {
        $this->nodeCount = $nodeCount;
        $this->nodeIndex = $nodeIndex;
    }

    public function branch()
    {
        return 'main';
    }

    public function buildID()
    {
        return 'abcd';
    }

    public function commit()
    {
        return 'abcd123';
    }

    public function detect()
    {
        return true;
    }

    public function message()
    {
        return 'Initial commit';
    }

    public function nodeCount()
    {
        return $this->nodeCount;
    }

    public function nodeIndex()
    {
        return $this->nodeIndex;
    }
}
