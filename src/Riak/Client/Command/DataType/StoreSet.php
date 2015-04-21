<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\Builder\StoreSetBuilder;
use Riak\Client\Core\Operation\DataType\StoreSetOperation;

/**
 * Command used to update or create a set datatype in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\DataType\FetchSet;
 *
 *  $namespace = new RiakNamespace('set_type', 'set_bucket');
 *  $location  = new RiakLocation($namespace, 'set_key');
 *  $command   = FetchSet::builder()
 *      ->withLocation($location)
 *      ->withReturnBody(true)
 *      ->add("Toronto")
 *      ->add("Ottawa")
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\DataType\Response\StoreSetResponse
 *  // @var $datatype \Riak\Client\Core\Query\Crdt\RiakSet
 *  $response = $client->execute($command);
 *  $datatype = $response->getDatatype();
 *
 *  var_dump($datatype->getValue());
 *  // ["Ottawa","Toronto"]
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSet extends StoreDataType
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $op        = $this->update->getOp();
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreSetOperation($converter, $this->location, $op, $this->context, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchSetBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new StoreSetBuilder($location, $options);
    }
}
