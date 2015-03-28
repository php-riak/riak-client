===========
Map Reduce
===========

This tutorial documentation its based on the `Basho MapReduce Docs`_.


.. _reference-mapreduce-how-it-works:

-------------
How it Works
-------------
The MapReduce framework helps developers divide a query into steps, divide the dataset into chunks, and then run those step/chunk pairs in separate physical hosts.

In Riak, MapReduce is one of the primary methods for non-primary-key-based querying in Riak, alongside secondary indexes. Riak allows you to run MapReduce jobs using Erlang or JavaScript.

There are two steps in a MapReduce query:

* ``Map`` — The data collection phase, which breaks up large chunks of work into smaller ones and then takes action on each chunk. Map phases consist of a function and a list of objects on which the map operation will operate.
* ``Reduce`` — The data collation or processing phase, which combines the results from the map step into a single output. The reduce phase is optional.


Riak MapReduce queries have two components:

* A list of inputs
* A list of phases

The elements of the input list are object locations as specified by bucket type, bucket, and key. The elements of the phases list are chunks of information related to a map, a reduce, or a link function.


-------------------
MapReduce Commands
-------------------

* :ref:`reference-mapreduce-bucket` Map-Reduce operation over a bucket in Riak.
* :ref:`reference-mapreduce-bucketkey` Map-Reduce operation over a specific set of keys in a bucket.
* :ref:`reference-mapreduce-index` Map-Reduce operation using a secondary index (2i) as input.
* :ref:`reference-mapreduce-search` Map-Reduce operation with a search query as input.


.. _reference-mapreduce-bucket:

``BucketMapReduce``
-------------------

Command used to perform a Map Reduce operation over a bucket in Riak.

Here is the general syntax for setting up a bucket map reduce combination to handle a range of keys:

.. code-block:: php

    <?php
    use Riak\Client\Command\MapReduce\IndexMapReduce;
    use Riak\Client\Core\Query\Func\NamedJsFunction;
    use Riak\Client\Command\MapReduce\KeyFilters;

    $map       = new NamedJsFunction('Riak.mapValuesJson');
    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $filter    = KeyFilters::filter()->between('key1', 'key9', false);
    $command   = BucketMapReduce::builder()
        ->withMapPhase($map, null, true)
        ->withNamespace($namespace)
        ->withKeyFilter($filter)
        ->build();

    /* @var $result \Riak\Client\Command\MapReduce\Response\BucketMapReduceResponse */
    /* @var $values \Riak\Client\Command\MapReduce\Response\MapReduceEntry[] */
    $result = $this->client->execute($command);
    $values = $result->getResultForPhase(0);

    echo $values[0]->getPhase();
    // 0
    var_dump($values[0]->getResponse());
    // ... first element response

See `Basho KeyFilters Docs`_. for more details on filters

.. _reference-mapreduce-bucketkey:

``BucketKeyMapReduce``
----------------------


.. _reference-mapreduce-index:

``IndexMapReduce``
------------------


.. _reference-mapreduce-search:

``SearchMapReduce``
-------------------



.. _`Basho MapReduce Docs`: http://docs.basho.com/riak/latest/dev/advanced/mapreduce/

.. _`Basho KeyFilters Docs`: http://docs.basho.com/riak/latest/dev/references/keyfilters/#Predicate-functions