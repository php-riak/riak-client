Performance
===========


This library is faster than most riak clients written in php,
It is about 50% percent faster is some cases, mostly because it uses protocol buffer and and iterators every where it is possible.

--------------------
Fetch/Get Operation
--------------------

.. image:: https://raw.githubusercontent.com/FabioBatSilva/riak-clients-performance-comparison/master/graphs/fetch-top.jpg

+---------------------+------------+-----------------+-------------+
| Fetch/Get Operation | Iterations | Average Time    | Ops/second  |
+---------------------+------------+-----------------+-------------+
| php_riak extension  |  1,000     | 0.0005543117523 | 1,804.03897 +
+---------------------+------------+-----------------+-------------+
| riak-client (proto) |  1,000     | 0.0007790112495 | 1,283.67851 +
+---------------------+------------+-----------------+-------------+
| basho/riak          |  1,000     | 0.0017048845291 | 586.54999   +
+---------------------+------------+-----------------+-------------+


--------------------
Store/Put Operation
--------------------

.. image:: https://raw.githubusercontent.com/FabioBatSilva/riak-clients-performance-comparison/master/graphs/store-top.jpg

+---------------------+------------+-----------------+-------------+
| Store/Put Operation | Iterations | Average Time    | Ops/second  |
+---------------------+------------+-----------------+-------------+
| php_riak extension  |  1,000     | 0.0010141553879 | 986.04219   +
+---------------------+------------+-----------------+-------------+
| riak-client (proto) |  1,000     | 0.0013224580288 | 756.16767   +
+---------------------+------------+-----------------+-------------+
| basho/riak          |  1,000     | 0.0025912058353 | 385.92071   +
+---------------------+------------+-----------------+-------------+



For more details and riak clients performance comparison see : https://github.com/FabioBatSilva/riak-clients-performance-comparison
