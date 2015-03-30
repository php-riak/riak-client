===========
Data Types
===========

This tutorial documentation its based on the `Basho CRDT Docs`_.
If you are not familiar with crdt in Riak Before you start take a look at `Basho CRDT Docs`_ for  more details.



In versions 2.0 and greater, Riak users can make use of a variety of Riak-specific data types inspired by research on convergent replicated data types, more commonly known as CRDTs.


---------
Location
---------

Here is the general syntax for setting up a bucket type/bucket/key combination to handle a data type:

.. code-block:: php

    <?php
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('<bucket_type>', '<bucket_name>');
    $location  = new RiakLocation($namespace, '<key>');


.. _reference-crdt-counters:

---------
Counters
---------

Counters are a bucket-level Riak Data Type that can be used either by themselves, i.e. associated with a bucket/key pair, or within a map. The examples in this section will show you how to use counters on their own.


Let's say that we want to create a counter called ``traffic_tickets`` in our counters bucket to keep track of our legal misbehavior. We can create this counter and ensure that the counters bucket will use our counters bucket type like this:

.. code-block:: php

    <?php
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('counters', 'counters');
    $location  = new RiakLocation($namespace, 'traffic_tickets');


Now that our client knows which bucket/key pairing to use for our counter,
``traffic_tickets`` will start out at 0 by default.
If we happen to get a ticket that afternoon, we would need to increment the counter

.. code-block:: php

    use Riak\Client\Command\DataType\StoreCounter;

    <?php
    // Increment the counter by 1 and fetch the current value
    $store = StoreCounter::builder()
        ->withReturnBody(true)
        ->withLocation($location)
        ->withDelta(1)
        ->build();

    $result  = $client->execute($store);
    $counter = $result->getDatatype();
    $value   = $counter->getValue();

    echo $value;
    // 1


If we're curious about how many tickets we have accumulated, we can simply retrieve the value of the counter at any time:

.. code-block:: php

    use Riak\Client\Command\DataType\FetchCounter;

    <?php
    // fetch counter
    $fetch = FetchCounter::builder()
        ->withLocation($location)
        ->withR(1)
        ->build();

    $result  = $client->execute($store);
    $counter = $result->getDatatype();
    $value   = $counter->getValue();


For a counter to be useful, you need to be able to decrement it in addition to incrementing it. Riak counters enable you to do precisely that. Let's say that we hire an expert lawyer who manages to get one of our traffic tickets stricken from our record:

.. code-block:: php

    use Riak\Client\Command\DataType\StoreCounter;

    <?php
    $store = StoreCounter::builder()
        ->withLocation($location)
        ->withDelta(-1)
        ->build();

    $client->execute($store);


.. _reference-crdt-sets:

-----
Sets
-----

As with counters (and maps, as shown below), using sets involves setting up a bucket/key pair to house a set and running set-specific operations on that pair.

Here is the general syntax for setting up a bucket type/bucket/key combination to handle a set:

.. code-block:: php

    <?php
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('sets', 'travel');
    $location  = new RiakLocation($namespace, 'cities');


Let's say that we read a travel brochure saying that Toronto and Montreal are nice places to go.
Let's add them to our cities set:


.. code-block:: php

    <?php

    use Riak\Client\RiakOption;
    use Riak\Client\Command\DataType\StoreSet;

    // Store new cities and return the current value
    $store = StoreCounter::builder()
        ->withLocation($location)
        ->withReturnBody(true)
        ->build();

    $store->add("Toronto")
    $store->add("Montreal")

    $result = $client->execute($store);
    $set    = $set->getDatatype();
    $value  = $counter->getValue();

    var_dump($value);
    // ["Toronto", "Montreal"]


Later on, we hear that Hamilton and Ottawa are nice cities to visit in Canada,
but if we visit them, we won't have time to visit Montreal, so we need to remove it from the list. It needs to be noted here that removing an element from a set is a bit tricker than adding elements.


.. code-block:: php

    <?php

    use Riak\Client\RiakOption;
    use Riak\Client\Command\DataType\StoreSet;
    use Riak\Client\Command\DataType\FecthSet;

    $fetch = FecthSet::builder()
        ->withLocation($location)
        ->build();

    $fetchResult  = $client->execute($store);
    $fetchContext = $result->getContext();

    $store = StoreCounter::builder()
        ->withContext($fetchContext)
        ->withLocation($location)
        ->withReturnBody(true)
        ->build();

    $store->add("Ottawa");
    $store->add("Vancouver");
    $store->remove("Montreal");

    $result  = $client->execute($store);
    $set     = $result->getDatatype();
    $value   = $set->getValue();

    var_dump($value);
    // ["Ottawa","Vancouver","Toronto"]


.. _reference-crdt-maps:

-----
Maps
-----

The map is in many ways the richest of the Riak Data Types because all of the other Data Types can be embedded within them, including maps themselves, to create arbitrarily complex custom Data Types out of a few basic building blocks


Let's say that we want to use Riak to store information about our company's customers. We'll use the bucket ``customers`` to do so. Each customer's data will be contained in its own key in the ``customers`` bucket. Let's create a map for the user Ahmed (``ahmed_info``) in our bucket and simply call it ``map`` for simplicity's sake:



.. code-block:: php

    <?php
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('maps', 'customers');
    $location  = new RiakLocation($namespace, 'ahmed_info');



Register & Flags Within Maps
-----------------------------

The first piece of info we want to store in our map is Ahmed's name and phone number, both of which are best stored as registers
We'll also create an  `enterprise_customer` flag to track whether Ahmed has signed up for the new plan:


.. code-block:: php

    <?php

    use Riak\Client\RiakOption;
    use Riak\Client\Command\DataType\StoreMap;

    $store = StoreMap::builder($location)
        ->updateRegister('first_name', 'Ahmed')
        ->updateRegister('phone_number', '5551234567')
        ->updateFlag('enterprise_customer', false)
        ->withReturnBody(true)
        ->build();

    $result = $client->execute($store);
    $map    = $result->getDatatype();

    echo $map->get('first_name');
    // Ahmed
    echo $map->get('phone_number');
    // 5551234567
    echo $map->get('enterprise_customer');
    // false


We can retrieve the value of that flag at any time:

.. code-block:: php

    <?php

    use Riak\Client\RiakOption;
    use Riak\Client\Command\DataType\FetchMap;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->build();

    $result = $client->execute($fetch);
    $map    = $result->getDatatype();
    $value  = $map->getValue();

    echo $map->get('first_name');
    echo $map->get('phone_number');
    echo $map->get('enterprise_customer');



Counters Within Maps
---------------------

We also want to know how many times Ahmed has visited our website.
We'll use a ``page_visits`` counter for that and run the following operation when Ahmed visits our page for the first time:


.. code-block:: php

    <?php

    use Riak\Client\RiakOption;
    use Riak\Client\Command\DataType\StoreMap;

    $store = StoreMap::builder()
        ->withLocation($location)
        ->updateCounter('page_visits', 1)
        ->build();

    $client->execute($store);



Sets Within Maps
-----------------

We'd also like to know what Ahmed's interests are so that we can better design a user experience for him.
Through his purchasing decisions, we find out that Ahmed likes robots, opera, and motorcyles. We'll store that information in a set inside of our map:

.. code-block:: php

    <?php

    use Riak\Client\Command\DataType\StoreMap;

    $store = StoreMap::builder()
        ->withLocation($location)
        ->updateSet('interests', ['robots', 'opera' , 'motorcycles'])
        ->build();

    $client->execute($store);


We learn from a recent purchasing decision that Ahmed actually doesn't seem to like opera.
He's much more keen on indie pop. Let's change the interests set to reflect that:

.. code-block:: php

    <?php

    use Riak\Client\Command\DataType\FetchMap;
    use Riak\Client\Command\DataType\StoreMap;
    use Riak\Client\Command\DataType\SetUpdate;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->build();

    $fetchResult  = $client->execute($fetch);
    $fetchContext = $fetchResult->getContext();
    $setUpdate    = new SetUpdate();

    $setUpdate->remove('opera');

    $store = StoreMap::builder()
        ->withLocation($location)
        ->withContext($fetchContext)
        ->updateSet('interests', $setUpdate)
        ->withReturnBody(true)
        ->build();

    $result = $client->execute($store);
    $map    = $result->getDatatype();

    var_dump($map->get('interests'));
    // ['robots', 'motorcycles']



Maps Within Maps
-----------------

We've stored a wide of variety of information—of a wide variety of types—within the ``ahmed_info`` map thus far, but we have yet to explore recursively storing maps within maps (which can be nested as deeply as you wish).

Our company is doing well and we have lots of useful information about Ahmed, but now we want to store information about Ahmed's contacts as well. We'll start with storing some information about Ahmed's colleague Annika inside of a map called ``annika_info``.

First, we'll store Annika's first name, last name, and phone number in registers:

.. code-block:: php

    <?php

    use Riak\Client\Command\DataType\FetchMap;
    use Riak\Client\Command\DataType\StoreMap;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->build();

    $fetchResult  = $client->execute($fetch);
    $fetchContext = $fetchResult->getContext();

    $store = StoreMap::builder()
        ->withReturnBody(true)
        ->withContext($fetchContext)
        ->withLocation($location)
        ->updateMap('annika_info', [
            'first_name'   => 'Annika',
            'last_name'    => 'Weiss',
            'phone_number' => '5559876543'
        ])
        ->build();

    $result     = $client->execute($store);
    $map        = $result->getDatatype();
    $annikaInfo = $map->get('annika_info');

    echo $annikaInfo['first_name'];
    // Annika


Map values can also be removed:

.. code-block:: php

    <?php

    use Riak\Client\Command\DataType\FetchMap;
    use Riak\Client\Command\DataType\StoreMap;
    use Riak\Client\Command\DataType\MapUpdate;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->build();

    $fetchResult  = $client->execute($fetch);
    $fetchContext = $fetchResult->getContext();
    $mapUpdate    = new MapUpdate();

    $mapUpdate->removeRegister('first_name');

    $store = StoreMap::builder()
        ->updateMap('annika_info', $mapUpdate)
        ->withContext($fetchContext)
        ->withLocation($location)
        ->build();

    $client->execute($store);


Now, we'll store whether Annika is subscribed to a variety of plans within the company as well:

.. code-block:: php

    <?php

    use Riak\Client\Command\DataType\FetchMap;
    use Riak\Client\Command\DataType\StoreMap;
    use Riak\Client\Command\DataType\MapUpdate;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->build();

    $fetchResult  = $client->execute($fetch);
    $fetchContext = $fetchResult->getContext();
    $mapUpdate    = new MapUpdate();

    $mapUpdate
        ->updateFlag('enterprise_plan', false)
        ->updateFlag('family_plan', false)
        ->updateFlag('free_plan', true);

    $store = StoreMap::builder()
        ->updateMap('annika_info', $mapUpdate)
        ->withContext($fetchContext)
        ->withLocation($location)
        ->build();

    $client->execute($store);


The value of a flag can be retrieved at any time:

.. code-block:: php

    <?php

    use Riak\Client\RiakOption;
    use Riak\Client\Command\DataType\FetchMap;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->build();

    $result     = $client->execute($fetch);
    $map        = $result->getDatatype();
    $annikaInfo = $map->get('annika_info');

    echo $annikaInfo['enterprise_plan'];
    // false



It's also important to track the number of purchases that Annika has made with our company. Annika just made her first widget purchase, w'll also store Annika's interests in a set:


.. code-block:: php

    <?php

    use Riak\Client\Command\DataType\FetchMap;
    use Riak\Client\Command\DataType\StoreMap;
    use Riak\Client\Command\DataType\MapUpdate;
    use Riak\Client\Command\DataType\SetUpdate;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->withIncludeContext(true)
        ->build();

    $fetchResult  = $client->execute($fetch);
    $fetchContext = $fetchResult->getContext();
    $mapUpdate    = new MapUpdate();
    $setUpdate    = new SetUpdate();

    $setUpdate
        ->add("tango dancing");

    $mapUpdate
        ->updateCounter('widget_purchases', 1)
        ->updateCounter('interests', $setUpdate);

    $store = StoreMap::builder()
        ->updateMap('annika_info', $mapUpdate)
        ->withContext($fetchContext)
        ->withLocation($location)
        ->build();

    $client->execute($store);


If we wanted to add store information about one of Annika's specific purchases, we could do so within a map:

.. code-block:: php

    <?php

    use Riak\Client\Command\DataType\FetchMap;
    use Riak\Client\Command\DataType\StoreMap;
    use Riak\Client\Command\DataType\MapUpdate;

    $fetch = FetchMap::builder()
        ->withLocation($location)
        ->build();

    $fetchResult  = $client->execute($fetch);
    $fetchContext = $fetchResult->getContext();
    $mapUpdate    = new MapUpdate();

    $mapUpdate
        ->updateMap('purchase', [
            'first_purchase' => true,             // flag
            'amount'         => "1271",           // register
            'items'          => ["large widget"], // set
        ]);

    $store = StoreMap::builder()
        ->updateMap('annika_info', $mapUpdate)
        ->withContext($fetchContext)
        ->withLocation($location)
        ->build();

    $client->execute($store);


.. _`Basho CRDT Docs`: http://docs.basho.com/riak/latest/dev/using/data-types
