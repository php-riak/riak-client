Key/Value commands
==================

.. _reference-command-kv-store-value:

``StoreValue``
---------------

.. code-block:: php

    <?php
    use Riak\Client\RiakOption;
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withOption(RiakOption::NOTFOUND_OK, true)
        ->withOption(RiakOption::R, 1)
        ->build();

    $result = $client->execute($fetch);
    $object = $result->getValue();


===========================  ==========
Parameter                    Type
===========================  ==========
RiakOption::RETURN_BODY      boolean
RiakOption::IF_NOT_MODIFIED  boolean
RiakOption::IF_NONE_MATCH    boolean
RiakOption::RETURN_HEAD      boolean
RiakOption::W                integer
RiakOption::PW               integer
RiakOption::DW               integer
===========================  ==========



.. _reference-command-kv-fetch-value:

``FetchValue``
---------------

.. code-block:: php

    <?php
    use Riak\Client\RiakOption;
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withOption(RiakOption::NOTFOUND_OK, true)
        ->withOption(RiakOption::R, 1)
        ->build();

    $result = $client->execute($fetch);
    $object = $result->getValue();


===========================  ==========
Parameter                    Type
===========================  ==========
RiakOption::BASIC_QUORUM     boolean
RiakOption::DELETED_VCLOCK   string
RiakOption::IF_MODIFIED      string
RiakOption::NOTFOUND_OK      boolean
RiakOption::PR               integer
RiakOption::R                integer
RiakOption::SLOPPY_QUORUM    boolean
RiakOption::TIMEOUT          integer
===========================  ==========


.. _reference-command-kv-delete-value:

``DeleteValue``
---------------

.. code-block:: php

    <?php
    use Riak\Client\RiakOption;
    use Riak\Client\Command\Kv\DeleteValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $location  = new RiakLocation($namespace, 'object_key');

    // delete object
    $delete  = DeleteValue::builder($location)
        ->withOption(RiakOption::PW, 1)
        ->withOption(RiakOption::W, 2)
        ->build();

    $this->client->execute($delete);


===========================  ==========
Parameter                    Type
===========================  ==========
RiakOption::NOTFOUND_OK      boolean
RiakOption::PR               integer
RiakOption::R                integer
RiakOption::RW               integer
RiakOption::W                integer
RiakOption::PW               integer
RiakOption::DW               integer
RiakOption::SLOPPY_QUORUM    boolean
RiakOption::TIMEOUT          integer
===========================  ==========

