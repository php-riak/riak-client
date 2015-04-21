<?php

namespace Riak\Client\Core\Message\Kv;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a list keys request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListKeysRequest extends Request
{
    public $timeout;
    public $bucket;
    public $type;
}
