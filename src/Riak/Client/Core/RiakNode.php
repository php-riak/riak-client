<?php

namespace Riak\Client\Core;


/**
 * This class represents a Riak Node.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakNode
{
    /**
     * @var \Riak\Client\Core\RiakTransport
     */
    private $adapter;

    /**
     * @return \Riak\Client\Core\RiakTransport
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param \Riak\Client\Core\RiakTransport $adapter
     */
    public function __construct(RiakTransport $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param \Riak\Client\Core\RiakOperation $operation
     *
     * @return \Riak\Client\RiakResponse
     */
    public function execute(RiakOperation $operation)
    {
        return $operation->execute($this->adapter);
    }
}
