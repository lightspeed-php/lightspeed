<?php

namespace Lightspeed;

use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as CommandsTestCommand;

class TestCommand extends CommandsTestCommand
{
    /**
     * {@inheritdoc}
     */
    protected function binary()
    {
        $command = ['vendor/lightspeedphp/lightspeed/bin/lightspeed'];

        if ('phpdbg' === PHP_SAPI) {
            return array_merge([PHP_BINARY, '-qrr'], $command);
        }

        return array_merge([PHP_BINARY], $command);
    }

    /**
     * {@inheritdoc}
     */
    protected function paratestArguments($options)
    {
        $options = array_values(array_filter($options, function ($option) {
            return !str_starts_with($option, '--env=')
                && $option != '--coverage'
                && $option != '-q'
                && $option != '--quiet'
                && $option != '--ansi'
                && $option != '--no-ansi'
                && !str_starts_with($option, '--min')
                && !str_starts_with($option, '-p')
                && !str_starts_with($option, '--parallel')
                && !str_starts_with($option, '--recreate-databases')
                && !str_starts_with($option, '--drop-databases')
                && !str_starts_with($option, '--without-databases');
        }));

        if (!file_exists($file = base_path('phpunit.xml'))) {
            $file = base_path('phpunit.xml.dist');
        }

        $options = array_merge($this->commonArguments(), [
            "--configuration=$file",
        ], $options);

        return $options;
    }

    /**
     * Get the array of environment variables common to all runners.
     *
     * @return array
     */
    protected function commonEnvironmentVariables()
    {
        return [
            'BUILDKITE_BRANCH' => $_SERVER['BUILDKITE_BRANCH'],
            'BUILDKITE_BUILD_NUMBER' => $_SERVER['BUILDKITE_BUILD_NUMBER'],
            'BUILDKITE_COMMIT' => $_SERVER['BUILDKITE_COMMIT'],
            'BUILDKITE' => $_SERVER['BUILDKITE'],
            'BUILDKITE_MESSAGE' => $_SERVER['BUILDKITE_MESSAGE'],
            'BUILDKITE_PARALLEL_JOB_COUNT' => $_SERVER['BUILDKITE_PARALLEL_JOB_COUNT'],
            'BUILDKITE_PARALLEL_JOB' => $_SERVER['BUILDKITE_PARALLEL_JOB'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function phpunitEnvironmentVariables()
    {
        return array_merge(
            $this->commonEnvironmentVariables(),
            parent::phpunitEnvironmentVariables(),
            parent::paratestEnvironmentVariables()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function paratestEnvironmentVariables()
    {
        return array_merge(
            $this->commonEnvironmentVariables(),
            parent::paratestEnvironmentVariables()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function isParallelDependenciesInstalled()
    {
        return true;
    }
}
