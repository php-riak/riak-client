<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a schema store request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class PutSchemaRequest extends Request
{
    public $name;
    public $content;
}
