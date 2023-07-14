<?php

namespace ThingyClient\Providers;

class BuildkiteProvider implements Provider
{
    public function branch()
    {
        return $_SERVER['BUILDKITE_BRANCH'];
    }

    public function buildID()
    {
        return $_SERVER['BUILDKITE_BUILD_NUMBER'];
    }

    public function commit()
    {
        return $_SERVER['BUILDKITE_COMMIT'];
    }

    public function detect()
    {
        return array_key_exists('BUILDKITE', $_SERVER) && $_SERVER['BUILDKITE'] === 'true';
    }

    public function message()
    {
        return $_SERVER['BUILDKITE_MESSAGE'];
    }

    public function nodeCount()
    {
        return $_SERVER['BUILDKITE_PARALLEL_JOB_COUNT'];
    }

    public function nodeIndex()
    {
        return $_SERVER['BUILDKITE_PARALLEL_JOB'];
    }
}
