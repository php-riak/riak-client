<?php

namespace Riak\Client\Command\Kv\Builder;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Kv\ListKeys;

/**
 * Used to construct a ListKeys command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListKeysBuilder extends Builder
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
    public function __construct(RiakNamespace $namespace = null, $timeout = null)
    {
        $this->namespace = $namespace;
        $this->timeout   = $timeout;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\Kv\Builder\ListKeysBuilder
     */
    public function withNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Set the Riak-side timeout value.
     *
     * @param integer $timeout
     *
     * @return \Riak\Client\Command\Kv\Builder\ListKeysBuilder
     */
    public function withTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Build a FetchValue object
     *
     * @return \Riak\Client\Command\Kv\ListKeys
     */
    public function build()
    {
        return new ListKeys($this->namespace, $this->timeout);
    }
}
