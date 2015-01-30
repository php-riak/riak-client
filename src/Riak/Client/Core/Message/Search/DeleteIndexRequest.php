<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a index delete request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteIndexRequest extends Request
{
    public $name;
}
