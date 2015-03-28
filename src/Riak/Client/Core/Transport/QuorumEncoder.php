<?php

namespace Riak\Client\Core\Transport;

/**
 * Encode/Decode Quorum Values
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
trait QuorumEncoder
{
    /**
     * @var array
     */
    private static $encode = [
        'default' => 4294967291,
        'all'     => 4294967292,
        'quorum'  => 4294967293,
        'one'     => 4294967294
    ];

    /**
     * @var array
     */
    private static $decode = [
        4294967291 => 'default',
        4294967292 => 'all',
        4294967293 => 'quorum',
        4294967294 => 'one'
    ];

    /**
     * @param mixed $value
     *
     * @return integer
     */
    protected function encodeQuorum($value)
    {
        if (isset(self::$encode[$value])) {
            return self::$encode[$value];
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return integer
     */
    protected function decodeQuorum($value)
    {
        if (isset(self::$decode[$value])) {
            return self::$decode[$value];
        }

        return $value;
    }
}
