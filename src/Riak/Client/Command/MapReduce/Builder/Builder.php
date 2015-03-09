<?php

namespace Riak\Client\Command\MapReduce\Builder;

use Riak\Client\Core\Query\Func\RiakFunction;
use Riak\Client\Command\MapReduce\Phase\MapPhase;
use Riak\Client\Command\MapReduce\Phase\LinkPhase;
use Riak\Client\Command\MapReduce\Phase\ReducePhase;

/**
 * Used to construct a Map-Reduce command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Builder
{
    /**
     * @var \Riak\Client\Command\MapReduce\MapReducePhase[]
     */
    protected $phases = [];

    /**
     * @var integer
     */
    protected $timeout;

    /**
     * Set the operations timeout
     *
     * @param integer $timeout
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Add a Map Phase
     *
     * @param \Riak\Client\Core\Query\Func\RiakFunction $function
     * @param mixed                                     $argument
     * @param boolean                                   $keepResult
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withMapPhase(RiakFunction $function, $argument = null, $keepResult = false)
    {
        $this->phases[] = new MapPhase($function, $argument, $keepResult);

        return $this;
    }

    /**
     * Add a Reduce Phase
     *
     * @param \Riak\Client\Core\Query\Func\RiakFunction $function
     * @param mixed                                     $argument
     * @param boolean                                   $keepResult
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withReducePhase(RiakFunction $function, $argument = null, $keepResult = false)
    {
        $this->phases[] = new ReducePhase($function, $argument, $keepResult);

        return $this;
    }

    /**
     * @param string  $bucket
     * @param string  $tag
     * @param boolean $keepResult
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withLinkPhase($bucket, $tag, $keepResult = false)
    {
        $this->phases[] = new LinkPhase($bucket, $tag, $keepResult);

        return $this;
    }

    /**
     * Build a riak Map-Reduce command object
     *
     * @return \Riak\Client\Command\MapReduce
     */
    abstract public function build();
}
