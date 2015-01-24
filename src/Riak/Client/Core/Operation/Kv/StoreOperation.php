<?php

namespace Riak\Client\Core\Operation\Kv;

use Riak\Client\Command\Kv\Response\StoreValueResponse;
use Riak\Client\Converter\DomainObjectReference;
use Riak\Client\Core\Message\Kv\PutRequest;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;
use Riak\Client\RiakConfig;

/**
 * An operation used to fetch an object from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreOperation implements RiakOperation
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
     * @var \Riak\Client\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\RiakConfig                  $config
     * @param \Riak\Client\Core\Query\RiakLocation     $location
     * @param \Riak\Client\Core\Query\RiakObject|mixed $value
     * @param array                                    $options
     */
    public function __construct(RiakConfig $config, RiakLocation $location, $value, array $options)
    {
        $this->location = $location;
        $this->options  = $options;
        $this->config   = $config;
        $this->value    = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $putRequest       = $this->createPutRequest();
        $putResponse      = $adapter->send($putRequest);
        $resolverFactory  = $this->config->getResolverFactory();
        $converterFactory = $this->config->getConverterFactory();
        $objectConverter  = $this->config->getRiakObjectConverter();

        $vClock      = $putResponse->vClock;
        $contentList = $putResponse->contentList;
        $values      = $objectConverter->convertToRiakObjectList($contentList, $vClock);
        $response    = new StoreValueResponse($converterFactory, $resolverFactory, $this->location, $values);

        $response->setGeneratedKey($putResponse->key);

        return $response;
    }

    /**
     * @return \Riak\Client\Core\Message\Kv\PutRequest
     */
    private function createPutRequest()
    {
        $request          = new PutRequest();
        $riakObject       = $this->getConvertedValue();
        $namespace        = $this->location->getNamespace();
        $objectConverter  = $this->config->getRiakObjectConverter();
        $vClockValue      = $riakObject->getVClock() ? $riakObject->getVClock()->getValue() : null;

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        $request->vClock  = $vClockValue;
        $request->key     = $this->location->getKey();
        $request->type    = $namespace->getBucketType();
        $request->bucket  = $namespace->getBucketName();
        $request->content = $objectConverter->convertToRiakContent($riakObject);

        return $request;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakObject
     */
    private function getConvertedValue()
    {
        if ($this->value instanceof RiakObject) {
            return $this->value;
        }

        if ($this->value === null) {
            return new RiakObject();
        }

        $type      = $this->getValueType();
        $factory   = $this->config->getConverterFactory();
        $converter = $factory->getConverter($type);
        $reference = new DomainObjectReference($this->value, $this->location);

        return $converter->fromDomain($reference);
    }

    /**
     * @return string
     */
    private function getValueType()
    {
        return is_object($this->value)
            ? get_class($this->value)
            : gettype($this->value);
    }
}
