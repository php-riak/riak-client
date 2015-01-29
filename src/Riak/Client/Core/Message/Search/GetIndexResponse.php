<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Response;

/**
 * This class represents a index fetch response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class GetIndexResponse extends Response
{
    public $nVal;
    public $name;
    public $schema;
}
