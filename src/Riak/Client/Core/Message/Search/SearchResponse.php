<?php

namespace Riak\Client\Core\Message\Search;

use Riak\Client\Core\Message\Response;

/**
 * This class represents a search response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchResponse extends Response
{
    public $docs = [];
    public $maxScore;
    public $numFound;
}
