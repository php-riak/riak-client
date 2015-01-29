<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a index store request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class PutIndexRequest extends Request
{
    public $nVal;
    public $name;
    public $schema;
}
