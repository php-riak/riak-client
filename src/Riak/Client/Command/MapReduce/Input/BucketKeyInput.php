<?php

namespace Riak\Client\Command\MapReduce\Input;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\MapReduce\Input\BucketKey\Input;

/**
 * Map-Reduce bucket keys input
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketKeyInput implements MapReduceInput
{
    /**
     * @var \Riak\Client\Command\MapReduce\Input\BucketKey\Input[]
     */
    private $inputs;

    /**
     * @param \Riak\Client\Command\MapReduce\Input\BucketKey\Input[] $inputs
     */
    public function __construct(array $inputs = [])
    {
        $this->inputs = $inputs;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param mixed                                $data
     */
    public function addLocation(RiakLocation $location, $data = null)
    {
        $this->inputs[] = new Input($location, $data);
    }

    /**
     * @return \Riak\Client\Command\MapReduce\Input\BucketKey\Input[]
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->inputs;
    }
}
