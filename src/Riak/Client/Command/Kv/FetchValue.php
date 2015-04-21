<?php

namespace Riak\Client\Command\Kv;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Operation\Kv\FetchOperation;
use Riak\Client\Command\Kv\Builder\FetchValueBuilder;

/**
 * Command used to fetch a value from Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\RiakOption;
 *  use Riak\Client\Command\Kv\FetchValue;
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *
 *  $namespace = new RiakNamespace('bucket_type', 'bucket_name');
 *  $location  = new RiakLocation($namespace, 'object_key');
 *  $command   = FetchValue::builder()
 *      ->withW(RiakOption::QUORUM)
 *      ->withPw(RiakOption::ONE)
 *      ->withLocation($location)
 *      ->withNotFoundOk(true)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Kv\Response\FetchValueResponse
 *  // @var $object \Riak\Client\Core\Query\RiakObject
 *  $response = $client->execute($command);
 *  $object   = $response->getValue();
 *
 *  var_dump($object->getValue());
 *  // '[1,1,1]'
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchValue implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $operation = new FetchOperation($config, $this->location, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     *
     * @return \Riak\Client\Command\Kv\Builder\FetchValueBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new FetchValueBuilder($location);
    }
}
