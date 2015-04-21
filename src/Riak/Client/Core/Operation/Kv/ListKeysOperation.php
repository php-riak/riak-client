<?php

namespace Riak\Client\Core\Operation\Kv;

use Riak\Client\Command\Kv\Response\ListKeysLocationIterator;
use Riak\Client\Command\Kv\Response\ListKeysResponse;
use Riak\Client\Core\Message\Kv\ListKeysRequest;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to list keys from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListKeysOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param \Riak\Client\Command\Kv\RiakNamespace $namespace
     * @param integer                               $timeout
     */
    public function __construct(RiakNamespace $namespace, $timeout = null)
    {
        $this->namespace = $namespace;
        $this->timeout   = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $request  = $this->createRequest();
        $response = $adapter->send($request);
        $iterator = new ListKeysLocationIterator($this->namespace, $response->iterator);

        return new ListKeysResponse($iterator);
    }

    /**
     * @return \Riak\Client\Core\Message\Kv\ListKeysRequest
     */
    private function createRequest()
    {
        $request   = new ListKeysRequest();

        $request->type    = $this->namespace->getBucketType();
        $request->bucket  = $this->namespace->getBucketName();
        $request->timeout = $this->timeout;

        return $request;
    }
}
