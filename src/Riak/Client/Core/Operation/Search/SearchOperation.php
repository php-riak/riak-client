<?php

namespace Riak\Client\Core\Operation\Search;

use Riak\Client\Command\Search\Response\SearchResponse;
use Riak\Client\Core\Message\Search\SearchRequest;
use Riak\Client\Core\Query\RiakSearchQuery;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * A Riak Search or Yokozuna query operation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Core\Query\RiakSearchQuery
     */
    private $query;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\Core\Query\RiakSearchQuery $query
     * @param array                                   $options
     */
    public function __construct(RiakSearchQuery $query, array $options = [])
    {
        $this->query   = $query;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $response = $adapter->send($this->createGetSearchRequest());

        return new SearchResponse($response->docs, $response->maxScore, $response->numFound);
    }

    /**
     * @return \Riak\Client\Core\Message\Search\SearchRequest
     */
    private function createGetSearchRequest()
    {
        $request = new SearchRequest();

        $request->q       = $this->query->getQuery();
        $request->index   = $this->query->getIndex();
        $request->presort = $this->query->getPresort();
        $request->rows    = $this->query->getNumRows();
        $request->filter  = $this->query->getFilterQuery();
        $request->fl      = $this->query->getReturnFields();

        return $request;
    }
}
