<?php

namespace Riak\Client\Core\Operation\Kv;

use Riak\Client\Command\Kv\Response\FetchValueResponse;
use Riak\Client\Core\Message\Kv\GetRequest;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;
use Riak\Client\RiakConfig;

/**
 * An operation used to fetch an object from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\RiakConfig
     */
    private $config;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\RiakConfig              $config
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakConfig $config, RiakLocation $location, array $options)
    {
        $this->location = $location;
        $this->options  = $options;
        $this->config   = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $getRequest       = $this->createGetRequest();
        $getResponse      = $adapter->send($getRequest);
        $resolverFactory  = $this->config->getResolverFactory();
        $converterFactory = $this->config->getConverterFactory();
        $objectConverter  = $this->config->getRiakObjectConverter();

        $vClock      = $getResponse->vClock;
        $unchanged   = $getResponse->unchanged;
        $contentList = $getResponse->contentList;
        $notFound    = empty($getResponse->contentList);
        $objectList  = $objectConverter->convertToRiakObjectList($contentList, $vClock);
        $response    = new FetchValueResponse($converterFactory, $resolverFactory, $this->location, $objectList);

        $response->setNotFound($notFound);
        $response->setUnchanged($unchanged);

        return $response;
    }

    /**
     * @return \Riak\Client\Core\Message\Kv\GetRequest
     */
    private function createGetRequest()
    {
        $request   = new GetRequest();
        $namespace = $this->location->getNamespace();

        $request->type   = $namespace->getBucketType();
        $request->bucket = $namespace->getBucketName();
        $request->key    = $this->location->getKey();

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        return $request;
    }
}
