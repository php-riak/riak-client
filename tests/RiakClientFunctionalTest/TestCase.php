<?php

namespace RiakClientFunctionalTest;

use GuzzleHttp\Client;
use Riak\Client\RiakCommand;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Transport\RiakTransportException;

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
        return getenv($name) ?: $default;
    }

    /**
     * @return \Riak\Client\RiakClient
     */
    protected function createRiakProtoClient()
    {
        return $this->createRiakClient($this->getEnv('RIAK_PROTO_URI', 'proto://127.0.0.1:8087'));
    }

    /**
     * @return \Riak\Client\RiakClient
     */
    protected function createRiakHttpClient()
    {
        return $this->createRiakClient($this->getEnv('RIAK_HTTP_URI', 'http://127.0.0.1:8098'));
    }

    /**
     * @param string $bucket
     * @param string $action
     *
     * @return string
     */
    protected function createInternalSolarBucketUri($bucket, $action)
    {
        // http://127.0.0.1:8093/internal_solr/test_riak_client_cats/select?q=name_s:Lion-o&wt=json&facet=on&facet.field=name_s

        return sprintf('%s/internal_solr/%s/%s', $this->getEnv('RIAK_SOLR_URI', 'http://127.0.0.1:8093'), $bucket, $action);
    }

    /**
     * @param string $baseUrl
     *
     * @return \GuzzleHttp\Client
     */
    protected function createGuzzleClient($baseUrl)
    {
        return new Client(['base_url'  => $baseUrl]);
    }

    /**
     * @param \Riak\Client\RiakCommand $command
     * @param integer                  $retryCount
     *
     * @return \Riak\Client\Command\Search\Response\FetchIndexResponse
     */
    protected function retryCommand(RiakCommand $command, $retryCount)
    {
        try {
            return $this->client->execute($command);
        } catch (RiakTransportException $exc) {

            if ($retryCount <= 0) {
                throw $exc;
            }

            sleep(1);

            return $this->retryCommand($command, -- $retryCount);
        }
    }

    /**
     * @param string $nodeUri
     *
     * @return \Riak\Client\RiakClient
     */
    protected function createRiakClient($nodeUri)
    {
        $nodeHost = parse_url($nodeUri, PHP_URL_HOST);
        $nodePort = parse_url($nodeUri, PHP_URL_PORT);

        if ((@fsockopen($nodeHost, $nodePort) === false)) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to riak : ' . $nodeUri);
        }

        $builder = new RiakClientBuilder();
        $client  = $builder
            ->withNodeUri($nodeUri)
            ->build();

        return $client;
    }
}
