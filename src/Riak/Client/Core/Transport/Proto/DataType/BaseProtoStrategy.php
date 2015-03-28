<?php

namespace Riak\Client\Core\Transport\Proto\DataType;

use Riak\Client\Core\Transport\Proto\ProtoClient;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;

/**
 * Base rpb strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseProtoStrategy extends ProtoStrategy
{
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
        parent::__construct($client);

        $this->opConverter  = $opConverter ?: new CrdtOpConverter();
    }
}
