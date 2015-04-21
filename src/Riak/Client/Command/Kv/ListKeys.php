<?php

namespace Riak\Client\Command\Kv;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Operation\Kv\ListKeysOperation;
use Riak\Client\Command\Kv\Builder\ListKeysBuilder;

/**
 * Command used to list the keys in a bucket.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Command\Kv\ListKeys;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *
 *  $namespace = new RiakNamespace('bucket_type', 'bucket_name');
 *  $command   = FetchValue::builder()
 *      ->withNamespace($namespace)
 *      ->$namespace(90)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Kv\Response\ListKeysResponse
 *  // @var $entries  \Riak\Client\Core\Query\RiakLocation[]
 *  $response  = $client->execute($command);
 *  $locations = $response->getLocations();
 *
 *  var_dump($locations[0]->getKey());
 *  // 'object_key'
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListKeys implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param \Riak\Client\Command\Kv\RiakNamespace $namespace
     * @param integer                               $timeout
     */
    public function __construct(RiakNamespace $namespace, $timeout = null)
    {
        $this->namespace = $namespace;
        $this->timeout   = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new ListKeysOperation($this->namespace, $this->timeout);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Command\Kv\RiakNamespace $namespace
     * @param integer                               $timeout
     *
     * @return \Riak\Client\Command\Kv\Builder\ListKeysBuilder
     */
    public static function builder(RiakNamespace $namespace = null, $timeout = null)
    {
        return new ListKeysBuilder($namespace, $timeout);
    }
}
