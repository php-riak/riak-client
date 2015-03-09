<?php

namespace Riak\Client\Command\MapReduce\Input\Index;

/**
 * Map-Reduce range criteria
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RangeCriteria implements IndexCriteria
{
    /**
     * @var mixed
     */
    private $start;

    /**
     * @var mixed
     */
    private $end;

    /**
     * @param mixed $start
     * @param mixed $end
     */
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'start' => $this->start,
            'end'   => $this->end,
        ];
    }
}
