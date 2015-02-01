<?php

namespace Riak\Client\Core\Message\Index;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a search request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexQueryRequest extends Request
{
    public $bucket;
    public $index;
    public $qtype;
    public $key;
    public $rangeMin;
    public $rangeMax;
    public $returnTerms;
    public $maxResults;
    public $continuation;
    public $timeout;
    public $type;
    public $termRegex;
    public $paginationSort;
}
