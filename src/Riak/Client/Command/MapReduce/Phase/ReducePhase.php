<?php

namespace Riak\Client\Command\MapReduce\Phase;

/**
 * A reduce phase of a MapReduce job spec.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ReducePhase extends FunctionPhase
{
    /**
     * {@inheritdoc}
     */
    public function getPhaseName()
    {
        return self::REDUCE;
    }
}
