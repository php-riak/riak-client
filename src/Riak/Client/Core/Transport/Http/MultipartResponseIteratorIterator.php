<?php

namespace Riak\Client\Core\Transport\Http;

use Riak\Client\Core\RiakIterator;

/**
 * Multipart stream iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class MultipartResponseIteratorIterator extends RiakIterator
{
    /**
     * @var \Riak\Client\Core\Transport\Http\MultipartResponseIterator
     */
    protected $iterator;

    /**
     * @var mixed
     */
    protected $json;

    /**
     * @param \Riak\Client\Core\Transport\Http\MultipartResponseIterator $iterator
     */
    public function __construct(MultipartResponseIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function readNext()
    {
        if ( ! $this->iterator->valid()) {
            return null;
        }

        $request = $this->iterator->current();
        $json    = $request->json();

        $this->json = $json;

        return $this->extract($json);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();

        parent::next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        parent::rewind();
    }

    /**
     * @param mixed $json
     *
     * @return mixed
     */
    abstract protected function extract($json);
}
