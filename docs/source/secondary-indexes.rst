==================
Secondary Indexes
==================

This tutorial documentation its based on the `Basho Secondary Indexes Docs`_.
If you are not familiar with search in Riak Before you start take a look at `Basho Secondary Indexes Docs`_ for  more details.


Secondary indexing (2i) in Riak gives developers the ability to tag an object stored in Riak,
at write time, with one or more queryable values. Those values can be either a binary or string,
such as sensor_1_data or admin_user or click_event, or an integer, such as 99 or 141121.

Since key/value data is completely opaque to 2i,
applications must attach metadata to objects that tell Riak 2i exactly which attribute(s) to index and what the values of those indexes should be.

Riak Search serves analogous purposes but is quite different because it parses key/value data itself and builds indexes on the basis of Solr schemas.


.. _reference-store-object-index:

------------------------------------
Store Object with Secondary Indexes
------------------------------------

In this example, the key ``john_smith`` is used to store user data in the bucket ``users``, which bears the ``default`` bucket type. 
Let's say that an application would like add a Twitter handle and an email address to this object as secondary indexes.


.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\StoreValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Core\Query\Index\RiakIndexBin;

    $namespace = new RiakNamespace('default', 'users');
    $location  = new RiakLocation($namespace, 'john_smith');
    $object    = new RiakObject();

    $object->setContentType('application/json');
    $object->setValue('{"name": "FabioBatSilva"}');
    $object->addIndex(new RiakIndexBin('twitter', ['jsmith123']));
    $object->addIndex(new RiakIndexBin('email', ['jsmith@basho.com']));

    $command = StoreValue::builder($location)
        ->withValue($object)
        ->withW(3)
        ->build();

    // store object
    $client->execute($command);


This has accomplished the following :

* The object has been stored with a primary bucket/key of ``users``/``john_smith``
* The object now has a secondary index called twitter_bin with a value of ``jsmith123``
* The object now has a secondary index called email_bin with a value of ``jsmith@basho.com``


.. _reference-query-object-index:


----------------------------------------
Querying Objects with Secondary Indexes
----------------------------------------

Let's query the ``users`` bucket on the basis of Twitter handle to make sure that we can find our stored object:


.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\BinIndexQuery;

    $namespace = new RiakNamespace('default', 'users');
    $command   = BinIndexQuery::builder()
        ->withNamespace($namespace)
        ->withIndexName('twitter')
        ->withMatch('jsmith123')
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    /** @var $entries array */
    $result  = $this->client->execute($command);
    $entries = $result->getEntries();

    echo $entries[0]->getLocation()->getKey();
    // john_smith


.. _reference-query-index:

---------
Querying
---------

Exact Match
-----------

The following examples perform an exact match index query.


Query a binary index:

.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\BinIndexQuery;

    $namespace = new RiakNamespace('bucket-type', 'bucket-name');
    $command   = BinIndexQuery::builder()
        ->withNamespace($namespace)
        ->withIndexName('index-name')
        ->withMatch('index-val')
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    $result  = $this->client->execute($command);


Query an integer index:

.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\IntIndexQuery;

    $namespace = new RiakNamespace('bucket-type', 'bucket-name');
    $command   = IntIndexQuery::builder()
        ->withNamespace($namespace)
        ->withIndexName('index-name')
        ->withMatch(101)
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    $result  = $this->client->execute($command);


Range
------

The following examples perform a range query:


Query a binary index :

.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\BinIndexQuery;

    $namespace = new RiakNamespace('bucket-type', 'bucket-name');
    $command   = BinIndexQuery::builder()
        ->withNamespace($namespace)
        ->withIndexName('index-name')
        ->withStart('val1')
        ->withEnd('val9')
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    $result  = $this->client->execute($command);


Query a integer index :

.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\BinIndexQuery;

    $namespace = new RiakNamespace('bucket-type', 'bucket-name');
    $command   = BinIndexQuery::builder()
        ->withNamespace($namespace)
        ->withIndexName('index-name')
        ->withStart(1)
        ->withEnd(100)
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    $result  = $this->client->execute($command);


Range with terms
----------------

When performing a range query, it is possible to retrieve the matched index values alongside the Riak keys using ``return_terms=true``.
An example from a small sampling of Twitter data with indexed hash tags:


Query a binary index :

.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\BinIndexQuery;

    $namespace = new RiakNamespace('bucket-type', 'bucket-name');
    $command   = BinIndexQuery::builder()
        ->withNamespace($namespace)
        ->withIndexName('index-name')
        ->withReturnTerms(true)
        ->withStart('val1')
        ->withEnd('val9')
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    $result  = $this->client->execute($command);


Pagination
-----------

When asking for large result sets, it is often desirable to ask the servers to return chunks of results instead of a firehose.
You can do so using max_results=<n>, where n is the number of results you'd like to receive.

Assuming more keys are available, a continuation value will be included in the results to allow the client to request the next page.
Here is an example of a range query with both return_terms and pagination against the same Twitter data set:


.. note::
    Index queries are always made using streaming,
    ``IndexQueryResponse#getIterator()`` will return a stream iterator
    that can be used to iterate over the response entries.

    Notice that is not possible to rewind a stream iterator,
    If you need to re-use the result use ``IndexQueryResponse#getEntries()`` instead.


.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\IntIndexQuery;

    $namespace = new RiakNamespace('bucket-type', 'bucket-name');
    $command   = IntIndexQuery::builder()
        ->withNamespace($namespace)
        ->withIndexName('index-name')
        ->withReturnTerms(true)
        ->withMaxResults(100)
        ->withStart(1)
        ->withEnd(99999)
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    /** @var $iterator \Iterator */
    $result   = $this->client->execute($command);
    $iterator = $result->getIterator();

    /** @var $entry \Riak\Client\Command\Index\Response\IndexEntry */
    foreach ($iterator as $entry) {
        /// ...
    }

After after iterating over the response entries
Take the continuation value from the previous result set and feed it back into the query

.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Index\IntIndexQuery;

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    $continuation = $result->getContinuation();
    $nextCommand  = IntIndexQuery::builder()
        ->withNamespace($namespace)
        ->withContinuation($continuation)
        ->withIndexName('index-name')
        ->withReturnTerms(true)
        ->withMaxResults(100)
        ->withStart(1)
        ->withEnd(99999)
        ->build();

    /** @var $result \Riak\Client\Command\Index\Response\IndexQueryResponse */
    $nextResult = $this->client->execute($nextCommand);

.. _`Basho Secondary Indexes Docs`: http://docs.basho.com/riak/latest/dev/advanced/2i/
