<?php

namespace Riak\Client\Core\Message\Kv;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a delete request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteRequest extends Request
{
    public $vClock;
    public $bucket;
    public $type;
    public $key;
    public $r;
    public $pr;
    public $rw;
    public $w;
    public $dw;
    public $pw;
    public $timeout;
    public $sloppyQuorum;
    public $nVal;
}
