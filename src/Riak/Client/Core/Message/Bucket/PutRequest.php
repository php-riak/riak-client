<?php

namespace Riak\Client\Core\Message\Bucket;

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
    public $allowMult;
    public $lastWriteWins;
    public $precommitHooks = [];
    public $postcommitHooks = [];
    public $chashKeyFunction;
    public $linkwalkFunction;
    public $oldVclock;
    public $youngVclock;
    public $bigVclock;
    public $smallVclock;
    public $pr;
    public $r;
    public $w;
    public $pw;
    public $dw;
    public $rw;
    public $basicQuorum;
    public $notfoundOk;
    public $backend;
    public $search;
    public $searchIndex;
    public $datatype;
    public $consistent;
}
