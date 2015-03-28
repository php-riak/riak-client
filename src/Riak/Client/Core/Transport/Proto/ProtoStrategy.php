<?php

namespace Riak\Client\Core\Transport\Proto;

use Riak\Client\Core\Transport\Strategy;
use Riak\Client\Core\Transport\Proto\ProtoClient;

/**
 * Base rpb strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class ProtoStrategy implements Strategy
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
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    protected $client;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient $client
     */
    public function __construct(ProtoClient $client)
    {
        $this->client = $client;
    }

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
