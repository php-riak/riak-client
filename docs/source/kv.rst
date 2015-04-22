Key/Value commands
==================

.. _reference-command-kv-store-value:

``StoreValue``
---------------

.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\StoreValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');
    $object    = new RiakObject();

    $object->setContentType('application/json');
    $object->setValue('{"name": "FabioBatSilva"}');

    // store object
    $store  = StoreValue::builder($location)
        ->withReturnBody(true)
        ->withPw(1)
        ->withW(2)
        ->build();

    $result = $client->execute($store);
    $object = $result->getValue();


.. _reference-command-kv-fetch-value:

``FetchValue``
---------------

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


.. _reference-command-kv-delete-value:

``DeleteValue``
---------------

.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\DeleteValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');

    // delete object
    $delete  = DeleteValue::builder($location)
        ->withPw(1)
        ->withW(2)
        ->build();

    $client->execute($delete);

    
.. _reference-command-kv-list-keys:

``ListKeys``
---------------

.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\ListKeys;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');

    /** @var $result \Riak\Client\Command\Kv\Response\ListKeysResponse */
    /** @var $locations \Riak\Client\Core\Query\RiakLocation[] */
    $result    = $client->execute($command);
    $locations = $result->getLocations();

    echo $locations[0]->getKey();
    // object_key
