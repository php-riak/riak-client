<?php

namespace Riak\Client\Core\Operation\Index;

use Riak\Client\Command\Index\Response\IndexEntryIterator;
use Riak\Client\Command\Index\Response\IndexQueryResponse;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Query\RiakIndexQuery;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * A Riak 2i index query operation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexQueryOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Core\Query\RiakIndexQuery
     */
    private $query;

    /**
     * @param \Riak\Client\Core\Query\RiakIndexQuery $query
     */
    public function __construct(RiakIndexQuery $query)
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $request   = $this->createIndexQueryRequest();
        $namespace = $this->query->getNamespace();
        $response  = $adapter->send($request);
        $iterator  = new IndexEntryIterator($namespace, $response->iterator);

        return new IndexQueryResponse($namespace, $iterator, $response->continuation);
    }

    /**
     * @return \Riak\Client\Core\Message\Index\IndexQueryRequest
     */
    private function createIndexQueryRequest()
    {
        $request = new IndexQueryRequest();

        $request->bucket         = $this->query->getNamespace()->getBucketName();
        $request->type           = $this->query->getNamespace()->getBucketType();
        $request->paginationSort = $this->query->getPaginationSort();
        $request->continuation   = $this->query->getContinuation();
        $request->returnTerms    = $this->query->getReturnTerms();
        $request->maxResults     = $this->query->getMaxResults();
        $request->termRegex      = $this->query->getTermFilter();
        $request->index          = $this->query->getIndexName();
        $request->timeout        = $this->query->getTimeout();

        if ($this->query->getMatch() !== null) {
            $request->key   = $this->query->getMatch();
            $request->qtype = 'eq';
        }

        if ($this->query->getMatch() === null) {
            $request->rangeMin = $this->query->getStart();
            $request->rangeMax = $this->query->getEnd();
            $request->qtype    = 'range';
        }

        return $request;
    }
}
