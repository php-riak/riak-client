<?php

namespace Riak\Client\Command\MapReduce\Phase;

/**
 * A Map Phase of a Map/Reduce job spec.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MapPhase extends FunctionPhase
{
    /**
     * {@inheritdoc}
     */
    public function getPhaseName()
    {
        return self::MAP;
    }
}
