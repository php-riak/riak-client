<?php

namespace Riak\Client\Core\Message\Kv;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a put request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class PutRequest extends Request
{
    public $bucket;
    public $type;
    public $nVal;
    public $key;
    public $vClock;
    public $content;
    public $w;
    public $dw;
    public $pw;
    public $returnBody;
    public $ifNotModified;
    public $ifNoneMatch;
    public $returnHead;
}
