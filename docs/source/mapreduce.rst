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

-------------------
``BucketMapReduce``
-------------------

Command used to perform a Map Reduce operation over a bucket in Riak.

Here is the general syntax for setting up a bucket map reduce combination to handle a range of keys:

.. code-block:: php

    <?php
    use Riak\Client\Command\MapReduce\BucketMapReduce;
    use Riak\Client\Core\Query\Func\NamedJsFunction;
    use Riak\Client\Command\MapReduce\KeyFilters;
    use Riak\Client\Core\Query\RiakNamespace;

    $map       = new NamedJsFunction('Riak.mapValuesJson');
    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $filter    = KeyFilters::filter()->between('key1', 'key9', false);
    $command   = BucketMapReduce::builder()
        ->withMapPhase($map, null, true)
        ->withNamespace($namespace)
        ->withKeyFilter($filter)
        ->build();

    /* @var $result \Riak\Client\Command\MapReduce\Response\BucketMapReduceResponse */
    $result = $this->client->execute($command);
    $values = $result->getResultForPhase(0);

    echo $values[0];
    // ... first element response

See `Basho KeyFilters Docs`_. for more details on filters

.. _reference-mapreduce-bucketkey:

----------------------
``BucketKeyMapReduce``
----------------------

Command used to perform a map reduce operation over a specific set of keys in a bucket.

Here is the general syntax for setting up a bucket map over a specific set of keys:

.. code-block:: php

    <?php
    use Riak\Client\Command\MapReduce\BucketKeyMapReduce;
    use Riak\Client\Core\Query\Func\AnonymousJsFunction;
    use Riak\Client\Core\Query\Func\ErlangFunction;
    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Core\Query\RiakLocation;

    $reduce = new ErlangFunction('riak_kv_mapreduce', 'reduce_sum');
    $map    = new AnonymousJsFunction('function(entry) {
        return [JSON.parse(entry.values[0].data)];
    }');

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $command   = BucketKeyMapReduce::builder([])
        ->withMapPhase($map)
        ->withReducePhase($reduce, null, true)
        ->withLocation(new RiakLocation($namespace, 'key1'))
        ->withLocation(new RiakLocation($namespace, 'key2'))
        ->withLocation(new RiakLocation($namespace, 'key3'))
        ->build();

    /* @var $result \Riak\Client\Command\MapReduce\Response\BucketKeyMapReduceResponse */
    $result = $this->client->execute($command);
    $values = $result->getResultForPhase(1);

    echo $values[0];
    // 10


.. _reference-mapreduce-index:

------------------
``IndexMapReduce``
------------------

Command used to perform a map reduce operation using a secondary index (2i) as input.

Here is the general syntax for setting up a bin secondary index map-reduce:

.. code-block:: php

    <?php
    use Riak\Client\Command\MapReduce\IndexMapReduce;
    use Riak\Client\Core\Query\Func\AnonymousJsFunction;
    use Riak\Client\Core\Query\Func\ErlangFunction;
    use Riak\Client\Core\Query\RiakNamespace;

    $reduce = new ErlangFunction('riak_kv_mapreduce', 'reduce_sort');
    $map    = new AnonymousJsFunction('function(entry) {
        return [JSON.parse(entry.values[0].data).email];
    }');

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $command   = IndexMapReduce::builder()
        ->withMapPhase($map)
        ->withReducePhase($reduce, null, true)
        ->withNamespace($namespace)
        ->withIndexBin('department_index')
        ->withMatchValue('dev')
        ->build();

    /* @var $result \Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse */
    $result = $this->client->execute($command);
    $values = $result->getResultsFromAllPhases();

    echo implode(",", $values);
    // fabio.bat.silva@gmail.com,dev@gmail.com,riak@basho.com,...


For a int secondary index map-reduce:

.. code-block:: php

    <?php
    use Riak\Client\Command\MapReduce\IndexMapReduce;
    use Riak\Client\Core\Query\Func\AnonymousJsFunction;
    use Riak\Client\Core\Query\Func\ErlangFunction;
    use Riak\Client\Core\Query\RiakNamespace;

    $reduce = new ErlangFunction('riak_kv_mapreduce', 'reduce_sort');
    $map    = new AnonymousJsFunction('function(entry) {
        return [JSON.parse(entry.values[0].data).email];
    }');

    $namespace = new RiakNamespace('bucket_type', 'bucket_name');
    $command   = IndexMapReduce::builder()
        ->withMapPhase($map)
        ->withReducePhase($reduce, null, true)
        ->withNamespace($namespace)
        ->withIndexInt('year')
        ->withRange(2010, 2015)
        ->build();

    /* @var $result \Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse */
    $result = $this->client->execute($command);

.. _reference-mapreduce-search:

-------------------
``SearchMapReduce``
-------------------

.. code-block:: php

    <?php
    use Riak\Client\Command\MapReduce\SearchMapReduce;
    use Riak\Client\Core\Query\Func\AnonymousJsFunction;
    use Riak\Client\Core\Query\Func\ErlangFunction;
    use Riak\Client\Core\Query\RiakNamespace;

    $reduce = new ErlangFunction('riak_kv_mapreduce', 'reduce_sort');
    $map    = new AnonymousJsFunction('function(entry) {
        return [JSON.parse(entry.values[0].data).email];
    }');

    $namespace = new RiakNamespace('cats_type', 'cats_bucket');
    $command   = SearchMapReduce::builder()
        ->withMapPhase($map)
        ->withReducePhase($reduce, null, true)
        ->withQuery('name_s:Snarf')
        ->withIndex('famous')
        ->build();

    /* @var $result \Riak\Client\Command\MapReduce\Response\SearchMapReduceResponse */
    /* @var $iterator \Iterator*/
    $result   = $this->client->execute($command);
    $iterator = $result->getIterator();

    /** @var $entry \Riak\Client\Command\MapReduce\Response\MapReduceEntry */
    foreach ($iterator as $entry) {
        echo $entry->getPhase();
        // 1

        echo $entry->getResponse();
        // ["Snarf"]
    }

.. note::
    Map-reduce operations are always made using streaming,
    ``Response#getIterator()`` will return a stream iterator
    that can be used to iterate over the response entries.

    Notice that is not possible to rewind a stream iterator,
    If you need to re-use the result use ``Response#getResults()`` instead.


.. _`Basho MapReduce Docs`: http://docs.basho.com/riak/latest/dev/advanced/mapreduce/

.. _`Basho KeyFilters Docs`: http://docs.basho.com/riak/latest/dev/references/keyfilters/#Predicate-functions