<?php

namespace ThingyClient\Providers;

interface Provider
{
    public function branch();
    public function buildID();
    public function commit();
    public function detect();
    public function message();
    public function nodeCount();
    public function nodeIndex();
}
