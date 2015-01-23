<?php

namespace Riak\Client\Core\Query\Index;

/**
 * Implementation used to access a Riak _bin indexes
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakIndexBin extends RiakIndex
{
    /**
     * {@inheritdoc}
     */
    public function addValue($value)
    {
        $this->values[] = (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'bin';
    }
}
