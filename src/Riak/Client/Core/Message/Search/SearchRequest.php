<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Request;

/**
 * This class represents a search request.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchRequest extends Request
{
    public $q;
    public $index;
    public $rows;
    public $start;
    public $sort;
    public $filter;
    public $df;
    public $op;
    public $fl;
    public $presort;
}
