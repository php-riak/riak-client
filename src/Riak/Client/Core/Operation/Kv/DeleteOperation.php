<?php

namespace Riak\Client\Core\Operation\Kv;

use Riak\Client\Command\Kv\Response\DeleteValueResponse;
use Riak\Client\Core\Message\Kv\DeleteRequest;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;
use Riak\Client\Core\Query\VClock;
use Riak\Client\RiakConfig;

/**
 * An operation used to delete an object from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteOperation implements RiakOperation
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
     * @var \Riak\Client\Core\Query\VClock
     */
    private $vClock;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\RiakConfig                    $config
     * @param \Riak\Client\Core\Query\RiakLocation       $location
     * @param array                                      $options
     * @param \Riak\Client\Core\Query\VClock             $vClock
     */
    public function __construct(RiakConfig $config, RiakLocation $location, array $options, VClock $vClock = null)
    {
        $this->location = $location;
        $this->options  = $options;
        $this->config   = $config;
        $this->vClock   = $vClock;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $putRequest       = $this->createDeleteRequest();
        $putResponse      = $adapter->send($putRequest);
        $resolverFactory  = $this->config->getResolverFactory();
        $converterFactory = $this->config->getConverterFactory();
        $objectConverter  = $this->config->getRiakObjectConverter();

        $vClock      = $putResponse->vClock;
        $contentList = $putResponse->contentList;
        $values      = $objectConverter->convertToRiakObjectList($contentList, $vClock);
        $response    = new DeleteValueResponse($converterFactory, $resolverFactory, $this->location, $values);

        return $response;
    }

    /**
     * @return \Riak\Client\Core\Message\Kv\DeleteRequest
     */
    private function createDeleteRequest()
    {
        $request   = new DeleteRequest();
        $namespace = $this->location->getNamespace();

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        $request->key     = $this->location->getKey();
        $request->type    = $namespace->getBucketType();
        $request->bucket  = $namespace->getBucketName();
        $request->vClock  = $this->vClock ? $this->vClock->getValue() : null;

        return $request;
    }
}
