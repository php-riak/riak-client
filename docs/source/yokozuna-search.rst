================
Yokozuna Search
================


This tutorial documentation its based on the `Basho Search Docs`_.
If you are not familiar with search in Riak Before you start take a look at `Basho Search Docs`_ for  more details.


Riak Search 2.0 is a new open-source project integrated with Riak. It allows for distributed, scalable, fault-tolerant, transparent indexing and querying of Riak values. It's easy to use. After connecting a bucket (or bucket type) to a Solr index, you simply write values (such as JSON, XML, plain text, Riak Data Types, etc.) into Riak as normal, and then query those indexed values using the Solr API.


Riak Search 2.0 is an integration of Solr (for indexing and querying) and Riak (for storage and distribution). There are a few points of interest that a user of Riak Search will have to keep in mind in order to properly store and later query for values.

Schemas explain to Solr how to index fields
Indexes are named Solr indexes against which you will query
Bucket-index association signals to Riak when to index values (this also includes bucket type-index association)


.. _reference-create-index:

-------------
Create Index
-------------

Let's start by creating an index called famous that uses the default schema.

.. code-block:: php

    <?php
    use Riak\Client\Command\Search\StoreIndex;
    use Riak\Client\Core\Query\Search\YokozunaIndex;

    $indexName = 'famous';
    $index     = new YokozunaIndex($indexName, '_yz_default');
    $command   = StoreIndex::builder()
        ->withIndex($index)
        ->build();

    $client->execute($command);




.. _reference-delete-index:

-----------------
Deleting Indexes
-----------------

Indexes may be deleted if they have no buckets associated with them:

.. code-block:: php

    <?php
    use Riak\Client\Command\Search\DeleteIndex;

    $indexName = 'famous';
    $command   = DeleteIndex::builder()
        ->withIndexName($indexName)
        ->build();

    $client->execute($command);


.. _reference-bucket-search-index:

--------------------
Bucket search index
--------------------

The last setup item that you need to perform is to associate either a bucket or a bucket type with a Solr index.
You only need do this once per bucket type, and all buckets within that type will use the same Solr index.


Although we recommend that you use all new buckets under a bucket type,
if you have existing data with a type-free bucket (i.e. under the default bucket type) you can set the search_index property for a specific bucket.

.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Command\Search\StoreIndex;
    use Riak\Client\Core\Query\BucketProperties;
    use Riak\Client\Command\Bucket\StoreBucketProperties;

    $namespace = new RiakNamespace("cats")
    $command   = StoreBucketProperties::builder()
        ->withSearchIndex('famous')
        ->withNamespace($namespace)
        ->build();

    $client->execute($command);


.. _reference-indexing-values:

----------------
Indexing Values
----------------

With a Solr schema, index, and association in place (and possibly a security setup as well),
we're ready to start using Riak Search. First, populate the cat bucket with values, in this case information about four cats: Liono, Cheetara, Snarf, and Panthro.


.. code-block:: php

    <?php

    use Riak\Client\Core\Query\RiakNamespace;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Command\Kv\StoreValue;

    $lionoObject    = new RiakObject();
    $cheetaraObject = new RiakObject();
    $snarfObject    = new RiakObject();
    $panthroObject  = new RiakObject();

    $lionoObject->setContentType('application/json');
    $lionoObject->setValue(json_encode([
        'name_s'   => 'Lion-o',
        'leader_b' => true,
        'age_i'    => 30,
    ]));

    $cheetaraObject->setContentType('application/json');
    $cheetaraObject->setValue(json_encode([
        'name_s'   => 'Cheetara',
        'leader_b' => false,
        'age_i'    => 30,
    ]));

    $snarfObject->setContentType('application/json');
    $snarfObject->setValue(json_encode([
        'name_s'   => 'Snarf',
        'leader_b' => false,
        'age_i'    => 43,
    ]));

    $panthroObject->setContentType('application/json');
    $panthroObject->setValue(json_encode([
        'name_s'   => 'Panthro',
        'leader_b' => false,
        'age_i'    => 36,
    ]));

    // All the store commands can be built the same way
    $namespace  = new RiakNamespace('default', 'cats');
    $location   = new RiakLocation($namespace, $key);
    $lionoStore = StoreValue::builder($location, $lionoObject)
        ->withPw(1)
        ->withW(2)
        ->build();

    // The other storage operations can be performed the same way
    $client->execute($lionoStore);


.. _reference-querying:

---------
Querying
---------

All distributed Solr queries are supported, which actually includes most of the single-node Solr queries.
This example searches for all documents in which the name_s value begins with Lion by means of a glob (wildcard) match.


.. code-block:: php

    <?php

    use Riak\Client\Command\Search\Search;

    $search = Search::builder()
        ->withQuery('name_s:Lion*')
        ->withIndex("famous")
        ->build();

    $searchResult  = $this->client->execute($search);
    $numResults    = $searchResult->getNumResults();
    $allResults    = $searchResult->getAllResults();
    $singleResults = $searchResult->getSingleResults();

    echo $numResults;
    // 1

    echo $singleResults[0]['name_s'];
    // Lion-o

    echo json_encode($allResults[0]['name_s']);
    // ["Lion-o"]

The response to a query will be an object containing details about the response,
such as a query's max score and a list of documents which match the given query.

.. note::
    While ``SearchResponse#getSingleResults()`` returns only the first entry of each element from the search query result.
    ``SearchResponse#getAllResults()`` will return a list containing all the result sets, so if you have a multi-valued field you should probably use ``getAllResults``

--------------
Range Queries
--------------

Range queries are searches within a range of numerical or date values.

To find the ages of all famous cats who are 30 or younger: ``age_i:[0 TO 30]``.
If you wanted to find all cats 30 or older, you could include a glob as a top end of the range: ``age_i:[30 TO *]``.

In this example the query fields are returned because they're stored in Solr.
This depends on your schema. If they are not stored, you'll have to perform a separate Riak GET operation to retrieve the value using the _yz_rk value.

.. code-block:: php

    <?php

    use Riak\Client\Command\Search\Search;
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $search = Search::builder()
        ->withQuery('age_i:[30 TO *]')
        ->withIndex("famous")
        ->build();

    /** @var $result \Riak\Client\Command\Search\Response\SearchResponse */
    /** @var $results array */
    $searchResult = $this->client->execute($search);
    $results      = $searchResult->getSingleResults();

    //  retrieve ``_yz_`` values
    $bucketType = $results[0]["_yz_rt"];
    $bucketName = $results[0]["yz_rb"];
    $key        = $results[0]["_yz_rk"];

    // create reference object locations
    $namespace = new RiakNamespace($bucketType , $bucketName;
    $location  = new RiakLocation($namespace, $key);

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withNotFoundOk(true)
        ->withR(1)
        ->build();

    /** @var $result \Riak\Client\Command\Kv\Response\FetchValueResponse */
    /** @var $object \Riak\Client\Core\Query\RiakObject */
    $result = $client->execute($fetch);
    $object = $result->getValue();

    echo $object->getValue();
    // {"name_s": "Lion-o", "age_i": 30, "leader_b": true}



.. _reference-pagination:

-----------
Pagination
-----------

A common requirement you may face is paginating searches,
where an ordered set of matching documents are returned in non-overlapping sequential subsets (in other words, pages).
This is easy to do with the start and rows parameters, where start is the number of documents to skip over (the offset) and rows are the number of results to return in one go.

For example, assuming we want two results per page, getting the second page is easy, where start is calculated as (rows per page) * (page number - 1).


.. code-block:: php

    <?php

    use Riak\Client\Command\Search\Search;

    $rowsPerPage = 2;
    $page        = 2;
    $start       = $rowsPerPage * ($page - 1);

    $search = Search::builder()
        ->withNumRows($rowsPerPage)
        ->withIndex("famous")
        ->withStart($start)
        ->withQuery('*:*')
        ->build();

    /** @var $result \Riak\Client\Command\Search\Response\SearchResponse */
    /** @var $results array */
    $searchResult = $this->client->execute($search);
    $results      = $searchResult->getAllResults();

.. _`Basho Search Docs`: http://docs.basho.com/riak/latest/dev/using/search/
