<?php

namespace Lightspeed\Providers;

class GitHubActionsProvider implements Provider
{
    public function branch()
    {
        return $_SERVER['GITHUB_REF_NAME'];
    }

    public function buildID()
    {
        return $_SERVER['GITHUB_RUN_ID'];
    }

    public function commit()
    {
        return $_SERVER['GITHUB_SHA'];
    }

    public function detect()
    {
        return array_key_exists('GITHUB_ACTIONS', $_SERVER) && $_SERVER['GITHUB_ACTIONS'] === 'true';
    }

    public function message()
    {
        return $_SERVER['LIGHTSPEED_MESSAGE'];
    }

    public function nodeCount()
    {
        return $_SERVER['LIGHTSPEED_NODE_COUNT'];
    }

    public function nodeIndex()
    {
        return $_SERVER['LIGHTSPEED_NODE_INDEX'];
    }
}
