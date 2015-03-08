<?php

namespace Riak\Client\Command\MapReduce\Phase;

/**
 * A Link Phase of a Map/Reduce job spec.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class LinkPhase extends MapReducePhase
{
    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @param string  $bucket
     * @param string  $tag
     * @param boolean $keepResult
     */
    public function __construct($bucket, $tag, $keepResult = false)
    {
        $this->keepResult = $keepResult;
        $this->bucket     = $bucket;
        $this->tag        = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhaseName()
    {
        return self::LINK;
    }
}
