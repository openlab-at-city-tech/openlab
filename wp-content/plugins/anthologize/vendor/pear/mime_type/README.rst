*********
MIME_Type
*********
PHP library to detect, parse and work with MIME types.

Features:

- Parse MIME types
- Supports full RFC 2045 specification
- Many utility functions for working with and determining info about types
- Most functions can be called statically
- Autodetect a file's mime-type, either with ``fileinfo`` extension,
  ``mime_magic`` extension, the 'file' command or an in-built file extension
  mapping list


Installation
============

PEAR
----
::

    $ pear install MIME_Type

Composer
--------
::

    $ composer require pear/mime_type


Usage
=====
See the examples in the ``docs/examples/`` directory and
the `official documentation`__.

__ http://pear.php.net/package/MIME_Type/docs

Detecting a file's MIME type
----------------------------
::

    <?php
    require_once 'MIME/Type.php';
    $type = MIME_Type::autoDetect('/path/to/file');
    if (PEAR::isError($type)) {
        echo 'Error: ' . $type->getMessage() . "\n";
        exit(1);
    } else {
        echo 'MIME type: ' . $type . "\n";
    }
    ?>


Links
=====
Homepage
  http://pear.php.net/package/MIME_Type
Bug tracker
  http://pear.php.net/bugs/search.php?cmd=display&package_name[]=MIME_Type
Documentation
  http://pear.php.net/package/MIME_Type/docs
Unit test status
  https://travis-ci.org/pear/MIME_Type

  .. image:: https://travis-ci.org/pear/MIME_Type.svg?branch=master
     :target: https://travis-ci.org/pear/MIME_Type


Development
===========

Updating extension mapping
--------------------------
The built-in extension-to-type mapping list can be updated from
apache's source code repository::

    $ ./scripts/update-mimelist.php
    ...
    785 new, 28 updated, 168 same, 5 own, 986 total
    Code updated

The file ``MIME/Type/Extension.php`` will be changed now.
