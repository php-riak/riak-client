<?php

namespace Riak\Client\Command\MapReduce\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\MapReduce\Specification;
use Riak\Client\Command\MapReduce\BucketKeyMapReduce;
use Riak\Client\Command\MapReduce\Input\BucketKeyInput;
use Riak\Client\Command\MapReduce\Input\BucketKey\Input;

/**
 * Used to construct a BucketKeyMapReduce command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketKeyMapReduceBuilder extends Builder
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
     *
     * @return \Riak\Client\Command\MapReduce\Input\BucketKeyInput
     */
    public function withLocation(RiakLocation $location, $data = null)
    {
        $this->inputs[] = new Input($location, $data);

        return $this;
    }

    /**
     * Build a Bucket Key Map-Reduce command object
     *
     * @return \Riak\Client\Command\MapReduce\Input\BucketKeyInput
     */
    public function build()
    {
        $input = new BucketKeyInput($this->inputs);
        $spec  = new Specification($input, $this->phases, $this->timeout);

        return new BucketKeyMapReduce($spec);
    }
}
