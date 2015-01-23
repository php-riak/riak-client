<?php

namespace Riak\Client\Core\Query\Crdt\Op;

/**
 * Riak Set crdt op.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SetOp implements CrdtOp
{
    /**
     * @var array
     */
    private $adds = [];

    /**
     * @var array
     */
    private $removes = [];

    /**
     * @param array $adds
     * @param array $removes
     */
    public function __construct(array $adds, array $removes)
    {
        $this->adds    = $adds;
        $this->removes = $removes;
    }

    /**
     * @return array
     */
    public function getAdds()
    {
        return $this->adds;
    }

    /**
     * @return array
     */
    public function getRemoves()
    {
        return $this->removes;
    }
}
