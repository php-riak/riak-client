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
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhaseName()
    {
        return self::LINK;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $data = [
            'bucket' => $this->bucket,
            'tag'    => $this->tag
        ];

        if ($this->keepResult != null) {
            $data['keep'] = $this->keepResult;
        }

        return $data;
    }
}
