<?php

namespace Riak\Client\Core\Message\Index;

use Riak\Client\Core\Message\StreamingResponse;

/**
 * This class represents a search response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexQueryResponse extends StreamingResponse
{
    public $continuation;
}
