<?php

namespace Riak\Client\Core\Operation\Index;

use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * A Map-Reduce Operation on Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MapReduceOperation implements RiakOperation
{
    /**
     * @var string
     */
    private $content;

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        return null;
    }
}
