<?php

namespace RiakClientFunctionalTest;

use Riak\Client\RiakCommand;

abstract class TestCase extends \RiakClientTest\TestCase
{
    /**
     * @var \Riak\Client\RiakClient
     */
    protected $client;

    /**
     * @return \Riak\Client\RiakClient
     */
    abstract protected function createClient();

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createClient();
    }

    /**
     * @param mixed $name
     * @param mixed $default
     *
     * @return string
     */
    protected function getEnv($name, $default)
    {
        return TestHelper::getEnv($name, $default);
    }

    /**
     * @return \Riak\Client\RiakClient
     */
    protected function createRiakProtoClient()
    {
        return TestHelper::createRiakProtoClient();
    }

    /**
     * @return \Riak\Client\RiakClient
     */
    protected function createRiakHttpClient()
    {
        return TestHelper::createRiakHttpClient();
    }

    /**
     * @param string $bucket
     * @param string $action
     *
     * @return string
     */
    protected function createInternalSolarBucketUri($bucket, $action)
    {
        return TestHelper::createInternalSolarBucketUri($bucket, $action);
    }

    /**
     * @param string $baseUrl
     *
     * @return \GuzzleHttp\Client
     */
    protected function createGuzzleClient($baseUrl)
    {
        return TestHelper::createGuzzleClient($baseUrl);
    }

    /**
     * @param \Riak\Client\RiakCommand $command
     * @param integer                  $retryCount
     *
     * @return \Riak\Client\Command\Search\Response\FetchIndexResponse
     */
    protected function retryCommand(RiakCommand $command, $retryCount)
    {
        return TestHelper::retryCommand($command, $retryCount);
    }

    /**
     * @param string $nodeUri
     *
     * @return \Riak\Client\RiakClient
     */
    protected function createRiakClient($nodeUri)
    {
        return TestHelper::createRiakClient($nodeUri);
    }
}
