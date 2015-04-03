===============
PHP Riak Client
===============


--------------------
Taste of Riak: PHP
--------------------

.. _reference-taste-of-client-setup:

If you haven't set up a Riak Node and started it, please visit the Prerequisites first.

-------------
Client Setup
-------------

Here is a `Composer`_ example::

    $ composer require "php-riak/riak-client"


If you are using a single local Riak node, use the following to create a new client instance,
assuming that the node is running on ``localhost`` port ``8087`` Protocol Buffers or port ``8098`` for http:

The easiest way to get started with the client is using a `RiakClientBuilder` :

.. code-block:: php

    <?php
    use Riak\Client\RiakClientBuilder;

    $builder = new RiakClientBuilder();
    $client  = $builder
        ->withNodeUri('http://192.168.1.1:8098')
        ->withNodeUri('proto://192.168.1.2:8087')
        ->build();

Once you have a ``$client`` we are now ready to start interacting with Riak.


.. _reference-taste-of-riak-create-object:

-------------------------
Creating Objects in Riak
-------------------------

The first object that we create is a very basic object with a content type of ``text/plain``.
Once that object is created, we create a ``StoreValue`` operation that will store the object later on down the line:


.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\StoreValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $object    = new RiakObject();
    $namespace = new RiakNamespace('default', 'quotes');
    $location  = new RiakLocation($namespace, 'icemand');

    $object->setValue("You're dangerous, Maverick");
    $object->setContentType('text/plain');

    // store object
    $store  = StoreValue::builder($location, $object)
        ->withPw(1)
        ->withW(2)
        ->build();

    // Use our client object to execute the store operation
    $client->execute($store);



.. _reference-taste-of-riak-read-object:

--------------------------
Reading Objects from Riak
--------------------------

After that, we check to make sure that the stored object has the same value as the object that we created.
This requires us to fetch the object by way of a ``FetchValue`` operation:


.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('default', 'quotes');
    $location  = new RiakLocation($namespace, 'icemand');

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
    // You're dangerous, Maverick



.. _reference-taste-of-riak-delete-object:

---------------------------
Deleting Objects from Riak
---------------------------

Now that we've stored and then fetched the object,
we can delete it by creating and executing a ``DeleteValue`` operation:

.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\DeleteValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('default', 'quotes');
    $location  = new RiakLocation($namespace, 'icemand');

    // delete object
    $delete  = DeleteValue::builder($location)
        ->withPw(1)
        ->withW(2)
        ->build();

    $this->client->execute($delete);



.. _reference-taste-of-riak-complex-objects:


-----------------------------
Working With Complex Objects
-----------------------------

Since the world is a little more complicated than simple integers and bits of strings,
let’s see how we can work with more complex objects.
Take for example, this plain PHP object that encapsulates some knowledge about a book.

.. code-block:: php

    <?php
    class Book implements \JsonSerializable
    {
        private $title;
        private $author;
        private $body;
        private $isbn;
        private $copiesOwned;

        // getter and setters.

        public function jsonSerialize()
        {
            return [
                'body'        => $this->body,
                'title'       => $this->title,
                'author'      => $this->author,
                'copiesOwned' => $this->copiesOwned,
            ];
        }
    }


By default, the PHP Riak client serializes PHP Objets as JSON.
Let's create a new Book object to store:


.. code-block:: php

    <?php
    $mobyDick = new Book();

    $modyDick->setTitle("Moby Dick");
    $mobyDick->setAuthor("Herman Melville");
    $mobyDick->setBody("Call me Ishmael. Some years ago...");
    $mobyDick->setIsbn("11119799723");
    $mobyDick->setCopiesOwned(3);



Now we can store that Object object just like we stored the riak object earlier:


.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\StoreValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('default', 'books');
    $location  = new RiakLocation($namespace, 'moby_dick');

    /** @var $mobyDick \Book */
    $store  = StoreValue::builder($location, $mobyDick)
        ->withPw(1)
        ->withW(2)
        ->build();

    $client->execute($store);


If we fetch the object using the same method we showed up above, we should get the following:

.. code-block:: php

    <?php
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('default', 'books');
    $location  = new RiakLocation($namespace, 'moby_dick');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withNotFoundOk(true)
        ->withR(1)
        ->build();

    /** @var $result \Riak\Client\Command\Kv\Response\FetchValueResponse */
    /** @var $object \Riak\Client\Core\Query\RiakObject */
    $result = $client->execute($fetch);
    $book   = $result->getValue('Book');
    $object = $result->getValue();

    echo $book->getTitle();
    //  "Moby Dick"

    echo $object->getValue();
    /*
    {
      "title": "Moby Dick",
      "author": "Herman Melville",
      "body": "Call me Ishmael. Some years ago...",
      "isbn": "1111979723",
      "copiesOwned": 3
    }
    */



-----------
Development
-----------


All development is done on Github_.
Use Issues_ to report problems or submit contributions.

.. _Github: https://github.com/php-riak/riak-client
.. _Issues: https://github.com/php-riak/riak-client/issues

.. _`Basho Taste of Riak Docs`: http://docs.basho.com/riak/latest/dev/taste-of-riak/java
.. _`Composer`: https://getcomposer.org


This tutorial documentation its based on the `Basho Taste of Riak Docs`_.



Contents
--------

.. toctree::
   :maxdepth: 1

   client
   bucket
   kv
   datatype
   secondary-indexes
   yokozuna-search
   mapreduce
   siblings
   performance
