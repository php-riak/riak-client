<?php

namespace Riak\Client\Command\MapReduce\Input\Index;

/**
 * Map-Reduce match criteria
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MatchCriteria implements IndexCriteria
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'key' => $this->value
        ];
    }
}
