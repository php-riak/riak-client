PHP Riak Client
===============

Installation
============

Add the client to your project using Composer_::

    $ composer require "fbs/riak-client"

.. _Riak: http://docs.basho.com/riak
.. _Composer: https://getcomposer.

Getting started
===============

The client
----------

The easiest way to get started with the client is using a `RiakClientBuilder` :

.. code-block:: php

    <?php
    use Riak\Client\RiakClientBuilder;

    $builder = new RiakClientBuilder();
    $client  = $builder
        ->withNodeUri('http://192.168.1.1:8098')
        ->withNodeUri('proto://192.168.1.2:8087')
        ->build();

Once you have a $client, commands from the ``Riak\Client\Command\*`` namespace are built then executed by the client.

Some basic examples of building and executing these commands is shown below.


Getting Data In
---------------

.. code-block:: php

    <?php
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\StoreValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $object    = new RiakObject();
    $namespace = new RiakNamespace('bucket_name', 'bucket_type');
    $location  = new RiakLocation($namespace, 'object_key');

    $object->setValue('[1,1,1]');
    $object->setContentType('application/json');

    // store object
    $store    = StoreValue::builder($location, $object)
        ->withOption(RiakOption::PW, 1)
        ->withOption(RiakOption::W, 2)
        ->build();

    $client->execute($store);

Getting Data Out
----------------

.. code-block:: php

    <?php
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_name', 'bucket_type');
    $location  = new RiakLocation($namespace, 'object_key');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withOption(RiakOption::NOTFOUND_OK, true)
        ->withOption(RiakOption::R, 1)
        ->build();

    $result = $client->execute($fetch);
    $object = $result->getValue();

Removing Data
-------------

.. code-block:: php

    <?php
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\DeleteValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('bucket_name', 'bucket_type');
    $location  = new RiakLocation($namespace, 'object_key');

    // delete object
    $delete  = DeleteValue::builder($location)
        ->withOption(RiakOption::PW, 1)
        ->withOption(RiakOption::W, 2)
        ->build();

    $this->client->execute($delete);


Development
-----------

All development is done on Github_.
Use Issues_ to report problems or submit contributions.

.. _Github: https://github.com/FabioBatSilva/riak-client
.. _Issues: https://github.com/FabioBatSilva/riak-client/issues


Contents
--------

.. toctree::
   :maxdepth: 1

   client
   kv
   siblings
