<?php

namespace Riak\Client\Core\Message\DataType;

use Riak\Client\Core\Message\Response as BaseResponse;

/**
 * Base class for all crdt responses.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Response extends BaseResponse
{
    public $type;
    public $value;
    public $context;
}
