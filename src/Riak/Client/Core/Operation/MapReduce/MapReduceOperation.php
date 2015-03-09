<?php

namespace Riak\Client\Core\Operation\MapReduce;

use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;
use Riak\Client\Core\Message\MapReduce\MapReduceRequest;
use Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse;

/**
 * A Map-Reduce Operation on Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MapReduceOperation implements RiakOperation
{
    /**
     * @var string
     */
    private $request;

    /**
     * @param string $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $request  = $this->createMapReduceRequest();
        $response = $adapter->send($request);

        return new IndexMapReduceResponse();
    }

    /**
     * @return \Riak\Client\Core\Message\MapReduce\MapReduceRequest
     */
    private function createMapReduceRequest()
    {
        $request = new MapReduceRequest();

        $request->request = $this->request;

        return $request;
    }
}
