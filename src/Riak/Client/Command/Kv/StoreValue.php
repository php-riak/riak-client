<?php

namespace Riak\Client\Command\Kv;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Operation\Kv\StoreOperation;
use Riak\Client\Command\Kv\Builder\StoreValueBuilder;

/**
 * Command used to store a value in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\RiakOption;
 *  use Riak\Client\Command\Kv\StoreValue;
 *  use Riak\Client\Core\Query\RiakObject;
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *
 *  $namespace = new RiakNamespace('bucket_type', 'bucket_name');
 *  $object    = new RiakObject('[1,1,1]', 'application/json');
 *  $location  = new RiakLocation($namespace, 'object_key');
 *  $command   = StoreValue::builder()
 *      ->withW(RiakOption::QUORUM)
 *      ->withPw(RiakOption::ONE)
 *      ->withLocation($location)
 *      ->withReturnBody(true)
 *      ->withValue($object)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Kv\Response\StoreValueResponse
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
class StoreValue implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\Core\Query\RiakLocation     $location
     * @param \Riak\Client\Core\Query\RiakObject|mixed $value
     * @param array                                    $options
     */
    public function __construct(RiakLocation $location, $value = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->value    = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $operation = new StoreOperation($config, $this->location, $this->value, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation     $location
     * @param \Riak\Client\Core\Query\RiakObject|mixed $value
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public static function builder(RiakLocation $location = null, $value = null)
    {
        return new StoreValueBuilder($location, $value);
    }
}
