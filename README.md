# Lightspeed

## Installation

Install with composer:

```shell
composer require --dev lightspeedphp/lightspeed
```

## Usage

Once installed, you can immediately run `php vendor/bin/lightspeed` to execute your tests. It will proxy to PHPUnit or
to Pest if you have it installed.

Lightspeed is best used within CI where it can run on multiple machines at once to parallelise the execution of your
tests. It currently supports GitHub Actions and Buildkite.

### GitHub Actions

Here is an example workflow file to execute your tests in parallel. Note you must set the env vars manually for tests
to be correctly split between jobs.

```yaml
name: Demo

on: ['push']

jobs:
  ci:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        node_count: [3]
        node_index: [0, 1, 2]

    name: Demo

    steps:
    - name: Checkout
      uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.0
        tools: composer:v2
        coverage: none

    - name: Install PHP dependencies
      run: composer update --no-interaction --no-progress

    - name: Tests (${{ matrix.node_index}})
      env:
        LIGHTSPEED_NODE_COUNT: ${{ matrix.node_count }}
        LIGHTSPEED_NODE_INDEX: ${{ matrix.node_index }}
      run: php vendor/bin/lightspeed --colors=always
```

### Buildkite

Buildkite is supported out of the box by setting the `parallelism` command step attribute. You can parallelise your
test steps with as much agents as you can to minimise test time.

## License

Lightspeed is licensed under the BSD 4-Clause License.
