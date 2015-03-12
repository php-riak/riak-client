<?php

namespace Riak\Client\Core\Operation\Bucket;

use Riak\Client\Command\Bucket\Response\ListBucketsResponse;
use Riak\Client\Command\Bucket\Response\ListBucketsIterator;
use Riak\Client\Core\Message\Bucket\ListRequest;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to list buckets in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListBucketsOperation implements RiakOperation
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param string  $type
     * @param integer $timeout
     */
    public function __construct($type = null, $timeout = null)
    {
        $this->type    = $type;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $request  = $this->createRequest();
        $response = $adapter->send($request);
        $iterator = new ListBucketsIterator($response->iterator);

        return new ListBucketsResponse($iterator);
    }

    /**
     * @return \Riak\Client\Core\Message\Bucket\ListRequest
     */
    private function createRequest()
    {
        $request = new ListRequest();

        $request->type    = $this->type;
        $request->timeout = $this->timeout;

        return $request;
    }
}
