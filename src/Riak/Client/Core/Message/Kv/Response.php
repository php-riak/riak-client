<?php

namespace Riak\Client\Core\Message\Kv;

use Riak\Client\Core\Message\Response as BaseResponse;

/**
 * Base class for all responses.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Response extends BaseResponse
{
    public $vClock;
    public $contentList = [];
}
