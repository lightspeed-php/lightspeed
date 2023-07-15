<?php

namespace ThingyClient;

use CurlHandle;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use ThingyClient\Providers\Provider;

class API
{
    protected Client $client;
    protected Provider $provider;
    protected CurlHandle $curl;

    public function __construct(Client $client, Provider $provider)
    {
        $this->client = $client;
        $this->provider = $provider;
        $this->curl = curl_init($_SERVER['THINGY_BASE_URL']);
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
        try {
            curl_reset($this->curl);
            curl_setopt($this->curl, CURLOPT_URL, $_SERVER['THINGY_BASE_URL'].'/api/v1/connect');
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode([
                'node_index' => $this->provider->nodeIndex(),
                'build_id' => $this->provider->buildID(),
            ]));
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization:Bearer '.$_SERVER['THINGY_API_TOKEN'], 'Accept:application/json']);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true );
            $body = curl_exec($this->curl);
            curl_close($this->curl);
            if (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) === 400) {
                return false;
            }

            // $response = $this->client
            //     ->post('/api/v1/connect', [
            //         'json' => [
            //             'node_index' => $this->provider->nodeIndex(),
            //             'build_id' => $this->provider->buildID(),
            //         ]
            //     ]);
            //
            // $body = (string) $response->getBody();
            $json = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(sprintf('Failed to decode json response body. Response: %s', $body));
            }
            return $json;
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    // no build is running
                    return false;
                case 422:
                    // validation error
                    throw $e;
                default:
                    throw $e;
            }
        }

        return false;
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
        curl_setopt($this->curl, CURLOPT_URL, $_SERVER['THINGY_BASE_URL'].'/api/v1/import');
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode([
            'commit' => $this->provider->commit(),
            'message' => $this->provider->message(),
            'branch' => $this->provider->branch(),
            'node_count' => $this->provider->nodeCount(),
            'node_index' => $this->provider->nodeIndex(),
            'build_id' => $this->provider->buildID(),
            'tests' => $tests,
        ]));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization:Bearer '.$_SERVER['THINGY_API_TOKEN'], 'Accept:application/json']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true );
        $body = curl_exec($this->curl);
        curl_close($this->curl);
        // $response = $this->client
        //     ->post('/api/v1/import', [
        //         'json' => [
        //             'commit' => $this->provider->commit(),
        //             'message' => $this->provider->message(),
        //             'branch' => $this->provider->branch(),
        //             'node_count' => $this->provider->nodeCount(),
        //             'node_index' => $this->provider->nodeIndex(),
        //             'build_id' => $this->provider->buildID(),
        //             'tests' => $tests,
        //         ]
        //     ]);
        //
        // $body = (string) $response->getBody();
        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Failed to decode json response body. Response: %s', $body));
        }

        return $json;
    }

    public function queue()
    {
        curl_reset($this->curl);
        curl_setopt($this->curl, CURLOPT_URL, $_SERVER['THINGY_BASE_URL'].'/api/v1/queue');
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode([
            'build_id' => $this->provider->buildID(),
        ]));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization:Bearer '.$_SERVER['THINGY_API_TOKEN'], 'Accept:application/json']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true );
        $body = curl_exec($this->curl);
        curl_close($this->curl);
        // $response = $this->client
        //     ->post('/api/v1/queue', [
        //         'json' => [
        //             'build_id' => $this->provider->buildID(),
        //         ]
        //     ]);
        //
        // $body = (string) $response->getBody();
        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(sprintf('Failed to decode json response body. Response: %s', $body));
        }
        // TODO: catch exceptions

        return $json;
    }
}
