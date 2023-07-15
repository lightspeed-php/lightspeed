<?php

namespace ThingyClient;

use CurlHandle;
use Exception;
use GuzzleHttp\Exception\ClientException;
use ThingyClient\Providers\Provider;

class API
{
    protected Provider $provider;
    protected CurlHandle $curl;
    protected array $opts;

    public function __construct(Provider $provider, array $opts)
    {
        $this->provider = $provider;
        $this->curl = curl_init($opts['url']);
        $this->opts = $opts;
    }

    /**
     * Connect to an existing build to get a list of test files to execute.
     *
     * This returns an array of test files if a build exists or false when it doesn't.
     *
     * @throws Exception
     * @throws ClientException
     * @return bool|array
     */
    public function connect()
    {
        curl_reset($this->curl);
        $opts = $this->buildCurl($this->opts['url'].'/api/v1/connect', [
            'node_index' => $this->provider->nodeIndex(),
            'build_id' => $this->provider->buildID(),
        ]);
        curl_setopt_array($this->curl, $opts);

        $body = curl_exec($this->curl);

        $this->handleErrors();

        if (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) === 400) {
            return false;
        }

        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Failed to decode json response body. Response: %s', $body));
        }

        return $json;
    }

    /**
     * Import a new build with all test files.
     *
     * This returns an array of test files to execute.
     *
     * @param  array<string> $tests
     * @throws Exception
     * @return array
     */
    public function import($tests)
    {
        curl_reset($this->curl);
        $opts = $this->buildCurl($this->opts['url'].'/api/v1/import', [
            'commit' => $this->provider->commit(),
            'message' => $this->provider->message(),
            'branch' => $this->provider->branch(),
            'node_count' => $this->provider->nodeCount(),
            'node_index' => $this->provider->nodeIndex(),
            'build_id' => $this->provider->buildID(),
            'tests' => $tests,
        ]);
        curl_setopt_array($this->curl, $opts);

        $body = curl_exec($this->curl);

        $this->handleErrors();

        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Failed to decode json response body. Response: %s', $body));
        }

        return $json;
    }

    public function queue()
    {
        curl_reset($this->curl);
        $opts = $this->buildCurl($this->opts['url'].'/api/v1/queue', [
            'build_id' => $this->provider->buildID(),
        ]);
        curl_setopt_array($this->curl, $opts);

        $body = curl_exec($this->curl);

        $this->handleErrors();

        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Failed to decode json response body. Response: %s', $body));
        }

        return $json;
    }

    protected function buildCurl(string $url, array $body)
    {
        return [
            CURLOPT_HTTPHEADER => [
                'Content-Type:application/json',
                'Authorization:Bearer '.$this->opts['api_token'],
                'Accept:application/json',
                'User-Agent:client+php version', // TODO
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        ];
    }

    protected function handleErrors()
    {
        $statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        switch ($statusCode) {
            case 401:
                throw new Exception('Unauthorized', $statusCode);
            case 403:
                throw new Exception('Forbidden', $statusCode);
            case 200:
                return;
        }
    }
}
