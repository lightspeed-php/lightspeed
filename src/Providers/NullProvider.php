<?php

namespace ThingyClient\Providers;

use ThingyClient\Providers\Provider;

class NullProvider implements Provider
{
    public  $buildId;

    public function __construct(string $buildId)
    {
        $this->buildId = $buildId;
    }

    public function branch()
    {
        return 'main';
    }

    public function buildID()
    {
        return $this->buildId;
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
        return 2;
    }

    public function nodeIndex()
    {
        return 0;
    }
}
