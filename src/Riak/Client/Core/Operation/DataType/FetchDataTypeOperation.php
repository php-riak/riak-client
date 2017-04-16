<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Converter\CrdtResponseConverter;
use Riak\Client\Core\Message\DataType\GetRequest;
use Riak\Client\Core\Query\Crdt\DataType;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to fetch a counter from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class FetchDataTypeOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Converter\CrdtResponseConverter
     */
    protected $converter;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    protected $location;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param \Riak\Client\Converter\CrdtResponseConverter $converter
     * @param \Riak\Client\Core\Query\RiakLocation         $location
     * @param array                                        $options
     */
    public function __construct(CrdtResponseConverter $converter, RiakLocation $location, array $options)
    {
        $this->converter = $converter;
        $this->location  = $location;
        $this->options   = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $getRequest  = $this->createRequest();
        $getResponse = $adapter->send($getRequest);
        return $this->createResponse($getResponse);
    }

    /**
     * @return \Riak\Client\Core\Message\DataType\GetRequest
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

    public function createRequest()
    {
        return $this->createGetRequest();
    }

    public function createResponse($response)
    {
        $datatype    = $this->converter->convert($response);
        $response    = $this->createDataTypeResponse($datatype, $response->context);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\DataType $datatype
     * @param string                                $context
     *
     * @return \Riak\Client\Command\DataType\Response\Response
     */
    abstract protected function createDataTypeResponse(DataType $datatype, $context = null);
}
