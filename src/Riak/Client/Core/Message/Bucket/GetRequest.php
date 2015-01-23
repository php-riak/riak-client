<?php

namespace Riak\Client\Core\Message\Bucket;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a get request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class GetRequest extends Request
{
    public $bucket;
    public $type;
}
