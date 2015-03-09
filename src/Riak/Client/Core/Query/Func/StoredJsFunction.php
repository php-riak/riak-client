<?php

namespace Riak\Client\Core\Query\Func;

/**
 * A Stored JS function.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoredJsFunction implements RiakFunction
{
    /**
     * @var string
     */
    private $bucket;

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $bucket
     * @param string $key
     */
    public function __construct($bucket, $key)
    {
        $this->bucket = $bucket;
        $this->key    = $key;
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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'language' => 'javascript',
            'bucket'   => $this->bucket,
            'key'      => $this->key
        ];
    }
}
