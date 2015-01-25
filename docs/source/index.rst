===============
PHP Riak Client
===============


.. _reference-taste-of-riak-instalation:

------------
Instalation
------------

To include the PHP Riak Client in your project,
add it to your project's dependencies.

Here is a `Composer`_ example::

    $ composer require "fbs/riak-client"



.. _reference-taste-of-riak-client:

----------
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

Once you have a ``$client``, commands from the ``Riak\Client\Command\*`` namespace are built then executed by the client.



.. _reference-taste-of-riak-create-object:

-------------------------
Creating Objects in Riak
-------------------------

The first object that we create is a very basic object with a content type of ``text/plain``. Once that object is created, we create a ``StoreValue`` operation that will store the object later on down the line:



.. code-block:: php

    <?php
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\StoreValue;
    use Riak\Client\Core\Query\RiakObject;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $object    = new RiakObject();
    $namespace = new RiakNamespace('quotes', 'default');
    $location  = new RiakLocation($namespace, 'icemand');

    $object->setValue("You're dangerous, Maverick");
    $object->setContentType('text/plain');

    // store object
    $store  = StoreValue::builder($location, $object)
        ->withOption(RiakOption::PW, 1)
        ->withOption(RiakOption::W, 2)
        ->build();

    // Use our client object to execute the store operation
    $client->execute($store);



.. _reference-taste-of-riak-read-object:

--------------------------
Reading Objects from Riak
--------------------------

After that, we check to make sure that the stored object has the same value as the object that we created. This requires us to fetch the object by way of a ``FetchValue`` operation:


.. code-block:: php

    <?php
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('quotes', 'default');
    $location  = new RiakLocation($namespace, 'icemand');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withOption(RiakOption::NOTFOUND_OK, true)
        ->withOption(RiakOption::R, 1)
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

Now that we've stored and then fetched the object, we can delete it by creating and executing a DeleteValue operation:

.. code-block:: php

    <?php
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\DeleteValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('quotes', 'default');
    $location  = new RiakLocation($namespace, 'icemand');

    // delete object
    $delete  = DeleteValue::builder($location)
        ->withOption(RiakOption::PW, 1)
        ->withOption(RiakOption::W, 2)
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
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\StoreValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('books', 'default');
    $location  = new RiakLocation($namespace, 'moby_dick');

    /** @var $mobyDick \Book */
    $store  = StoreValue::builder($location, $mobyDick)
        ->withOption(RiakOption::PW, 1)
        ->withOption(RiakOption::W, 2)
        ->build();

    $client->execute($store);


If we fetch the object using the same method we showed up above, we should get the following:

.. code-block:: php

    <?php
    use Riak\Client\Cap\RiakOption;
    use Riak\Client\Command\Kv\FetchValue;
    use Riak\Client\Core\Query\RiakLocation;
    use Riak\Client\Core\Query\RiakNamespace;

    $namespace = new RiakNamespace('books', 'default');
    $location  = new RiakLocation($namespace, 'moby_dick');

    // fetch object
    $fetch  = FetchValue::builder($location)
        ->withOption(RiakOption::NOTFOUND_OK, true)
        ->withOption(RiakOption::R, 1)
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


.. _reference-development

-----------
Development
-----------

All development is done on Github_.
Use Issues_ to report problems or submit contributions.

.. _Github: https://github.com/FabioBatSilva/riak-client
.. _Issues: https://github.com/FabioBatSilva/riak-client/issues

.. _`Basho Taste of Riak Docs`: http://docs.basho.com/riak/latest/dev/taste-of-riak/java
.. _`Composer`: https://getcomposer.org


This tutorial documentation its based on the `Basho Taste of Riak Docs`_.



Contents
--------

.. toctree::
   :maxdepth: 1

   client
   kv
   datatype
   siblings
