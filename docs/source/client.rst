Riak Client
===========

The easiest way to get started with the client is using a `RiakClientBuilder` :

.. code-block:: php

    <?php
    use Riak\Client\RiakClientBuilder;

    $builder = new RiakClientBuilder();
    $client  = $builder
        ->withNodeUri('http://192.168.1.1:8098')
        ->withNodeUri('proto://192.168.1.2:8087')
        ->build();


Once you have a $client, commands from the `Riak\Client\Command*` namespace are built then executed by the client.

.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withNotFoundOk(true)
        ->withR(1)
        ->build();

    $result = $client->execute($fetch);
    $object = $result->getValue();


RiakCommand classes
-------------------

* Fetching, storing and deleting objects

    * ``Riak\Client\Command\Kv\FetchValue``
    * ``Riak\Client\Command\Kv\StoreValue``
    * ``Riak\Client\Command\Kv\UpdateValue``
    * ``Riak\Client\Command\Kv\DeleteValue``

* Fetching and storing datatypes (CRDTs)

    * ``Riak\Client\Command\DataType\FetchCounter``
    * ``Riak\Client\Command\DataType\FetchSet``
    * ``Riak\Client\Command\DataType\FetchMap``
    * ``Riak\Client\Command\DataType\StoreCounter``
    * ``Riak\Client\Command\DataType\StoreSet``
    * ``Riak\Client\Command\DataType\StoreMap``

* Querying and modifying buckets

    * ``Riak\Client\Command\Bucket\FetchBucketProperties``
    * ``Riak\Client\Command\Bucket\StoreBucketProperties``

* Secondary index (2i)

    * ``Riak\Client\Command\Index\BinIndexQuery``
    * ``Riak\Client\Command\Index\IntIndexQuery``

* Yokozuna Search

    * ``Riak\Client\Command\Search\DeleteIndex``
    * ``Riak\Client\Command\Search\FetchIndex``
    * ``Riak\Client\Command\Search\StoreIndex``
    * ``Riak\Client\Command\Search\StoreSchema``
    * ``Riak\Client\Command\Search\FetchSchema``
    * ``Riak\Client\Command\Search\Search``
