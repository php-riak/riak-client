<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a index fetch request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class GetIndexRequest extends Request
{
    public $name;
}
