<?php

namespace Riak\Client\Core\Query\Crdt\Op;

/**
 * Riak Flag crdt op.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FlagOp implements CrdtOp
{
    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @param boolean $enabled
     */
    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
