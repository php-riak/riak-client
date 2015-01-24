<?php

namespace Riak\Client\Core\Transport\Proto\DataType;

use Riak\Client\Core\Transport\Strategy;
use Riak\Client\Core\Transport\Proto\ProtoClient;

/**
 * Base rpb strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseProtoStrategy implements Strategy
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    protected $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\CrdtOpConverter
     */
    protected $opConverter;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient $client
     * @param \Riak\Client\ProtoBuf\DtFetchReq              $opConverter
     */
    public function __construct(ProtoClient $client, $opConverter = null)
    {
        $this->client       = $client;
        $this->opConverter  = $opConverter ?: new CrdtOpConverter();
    }
}
