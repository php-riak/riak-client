<?php

namespace RiakClientFunctionalTest;

use GuzzleHttp\Client;
use Riak\Client\RiakClient;
use Riak\Client\RiakCommand;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Transport\RiakTransportException;

class TestHelper
{
    /**
     * @param mixed $name
     * @param mixed $default
     *
     * @return string
     */
    public static function getEnv($name, $default)
    {
        return getenv($name) ?: $default;
    }

    /**
     * @return \Riak\Client\RiakClient
     */
    public static function createRiakProtoClient()
    {
        return self::createRiakClient(self::getEnv('RIAK_PROTO_URI', 'proto://127.0.0.1:8087'));
    }

    /**
     * @return \Riak\Client\RiakClient
     */
    public static function createRiakHttpClient()
    {
        return self::createRiakClient(self::getEnv('RIAK_HTTP_URI', 'http://127.0.0.1:8098'));
    }

    /**
     * http://127.0.0.1:8093/internal_solr/test_riak_client_cats/select?q=name_s:Lion-o&wt=json&facet=on&facet.field=name_s
     *
     * @param string $bucket
     * @param string $action
     *
     * @return string
     */
    public static function createInternalSolarBucketUri($bucket, $action)
    {
        return sprintf('%s/internal_solr/%s/%s', self::getEnv('RIAK_SOLR_URI', 'http://127.0.0.1:8093'), $bucket, $action);
    }

    /**
     * @param string $baseUrl
     *
     * @return \GuzzleHttp\Client
     */
    public static function createGuzzleClient($baseUrl)
    {
        return new Client(['base_url'  => $baseUrl]);
    }

    /**
     * @param RiakClient  $client
     * @param RiakCommand $command
     * @param integer     $retryCount
     *
     * @return \Riak\Client\Command\Search\Response\FetchIndexResponse
     */
    public static function retryCommand(RiakClient $client, RiakCommand $command, $retryCount)
    {
        try {
            return $client->execute($command);
        } catch (RiakTransportException $exc) {

            if ($retryCount <= 0) {
                throw $exc;
            }

            sleep(1);

            return self::retryCommand($client, $command, -- $retryCount);
        }
    }

    /**
     * @param string $nodeUri
     *
     * @return \Riak\Client\RiakClient
     */
    public static function createRiakClient($nodeUri)
    {
        $nodeHost = parse_url($nodeUri, PHP_URL_HOST);
        $nodePort = parse_url($nodeUri, PHP_URL_PORT);

        if ((@fsockopen($nodeHost, $nodePort) == false)) {
            throw new \PHPUnit_Framework_SkippedTestError('The ' . __CLASS__ .' cannot connect to riak : ' . $nodeUri);
        }

        $builder = new RiakClientBuilder();
        $client  = $builder
            ->withNodeUri($nodeUri)
            ->build();

        return $client;
    }
}
