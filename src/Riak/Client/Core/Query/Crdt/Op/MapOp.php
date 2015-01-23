<?php

namespace Riak\Client\Core\Query\Crdt\Op;

/**
 * Riak Map crdt op.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MapOp implements CrdtOp
{
    /**
     * @var array
     */
    private $removes;

    /**
     * @var array
     */
    private $updates;

    /**
     * @param array $updates
     * @param array $removes
     */
    public function __construct(array $updates, array $removes)
    {
        $this->updates = $updates;
        $this->removes = $removes;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private function getRemoves($type)
    {
        return isset($this->removes[$type])
            ? $this->removes[$type]
            : [];
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private function getUpdates($type)
    {
        return isset($this->updates[$type])
            ? $this->updates[$type]
            : [];
    }

    /**
     * @return array
     */
    public function getMapRemoves()
    {
        return $this->getRemoves('map');
    }

    /**
     * @return array
     */
    public function getSetRemoves()
    {
        return $this->getRemoves('set');
    }

    /**
     * @return array
     */
    public function getFlagRemoves()
    {
        return $this->getRemoves('flag');
    }

    /**
     * @return array
     */
    public function getCounterRemoves()
    {
        return $this->getRemoves('counter');
    }

    /**
     * @return array
     */
    public function getRegisterRemoves()
    {
        return $this->getRemoves('register');
    }

    /**
     * @return array
     */
    public function getMapUpdates()
    {
        return $this->getUpdates('map');
    }

    /**
     * @return array
     */
    public function getSetUpdates()
    {
        return $this->getUpdates('set');
    }

    /**
     * @return array
     */
    public function getFlagUpdates()
    {
        return $this->getUpdates('flag');
    }

    /**
     * @return array
     */
    public function getCounterUpdates()
    {
        return $this->getUpdates('counter');
    }

    /**
     * @return array
     */
    public function getRegisterUpdates()
    {
        return $this->getUpdates('register');
    }
}
