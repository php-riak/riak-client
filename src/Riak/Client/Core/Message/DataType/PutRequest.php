<?php

namespace Riak\Client\Core\Message\DataType;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a put request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class PutRequest extends Request
{
    public $context;
    public $bucket;
    public $type;
    public $key;
    public $op;
    public $w;
    public $dw;
    public $pw;
    public $nVal;
    public $includeContext;
    public $sloppyQuorum;
    public $returnBody;
    public $timeout;
}
