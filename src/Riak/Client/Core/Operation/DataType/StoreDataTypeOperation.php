<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Converter\CrdtResponseConverter;
use Riak\Client\Core\Message\DataType\PutRequest;
use Riak\Client\Core\Query\Crdt\Op\CrdtOp;
use Riak\Client\Core\Query\Crdt\DataType;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to store a datatype in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class StoreDataTypeOperation implements RiakOperation
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
     * @var string
     */
    protected $context;

    /**
     * @var \Riak\Client\Core\Query\Crdt\Op\CrdtOp
     */
    protected $op;

    /**
     * @param \Riak\Client\Converter\CrdtResponseConverter $converter
     * @param \Riak\Client\Core\Query\RiakLocation         $location
     * @param \Riak\Client\Core\Query\Crdt\Op\CrdtOp       $op
     * @param string                                       $context
     * @param array                                        $options
     */
    public function __construct(CrdtResponseConverter $converter, RiakLocation $location, CrdtOp $op, $context, array $options)
    {
        $this->converter = $converter;
        $this->location  = $location;
        $this->options   = $options;
        $this->context   = $context;
        $this->op        = $op;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $putRequest  = $this->createGetRequest();
        $putResponse = $adapter->send($putRequest);
        $datatype    = $this->converter->convert($putResponse);
        $response    = $this->createDataTypeResponse($datatype, $putResponse->context);

        return $response;
    }

    /**
     * @return \Riak\Client\Core\Message\DataType\PutRequest
     */
    private function createGetRequest()
    {
        $request   = new PutRequest();
        $namespace = $this->location->getNamespace();

        $request->type    = $namespace->getBucketType();
        $request->bucket  = $namespace->getBucketName();
        $request->key     = $this->location->getKey();
        $request->context = $this->context;
        $request->op      = $this->op;

        foreach ($this->options as $name => $value) {
            $request->{$name} = $value;
        }

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\DataType $datatype
     * @param string                                $context
     *
     * @return \Riak\Client\Command\DataType\Response\Response
     */
    abstract protected function createDataTypeResponse(DataType $datatype = null, $context = null);
}
