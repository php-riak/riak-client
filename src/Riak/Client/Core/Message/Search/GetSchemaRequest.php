<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a schema fetch request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class GetSchemaRequest extends Request
{
    public $name;
}
