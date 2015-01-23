<?php

namespace Riak\Client\Core\Query\Index;

/**
 * Implementation used to access a Riak _int indexes
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakIndexInt extends RiakIndex
{
    /**
     * {@inheritdoc}
     */
    public function addValue($value)
    {
        $this->values[] = (int) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'int';
    }
}
