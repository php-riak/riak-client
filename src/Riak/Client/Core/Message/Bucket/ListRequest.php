<?php

namespace Riak\Client\Core\Message\Bucket;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a bucket list request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListRequest extends Request
{
    public $timeout;
    public $type;
}
