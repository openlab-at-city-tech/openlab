# PHP library for accessing the Digital Public Library of America API

This is a simple library for accessing the [DPLA search API](http://dp.la/info/developers/).

## Basic usage

To get started you simply need to require the main DPLA class and create an
instance of it, passing in your API key.

```php
require '/path/to/tfn/dpla.php';
$dpla = new \TFN\DPLA('your api key');
```

Using this object you can then create a search query. The query object supports
chaining if you wish to use it. This example runs a simple search for anything
mentioning pizza and gets the first page of ten results.

```php
$res = $dpla->createSearchQuery()->forText('pizza')->withPaging(1, 10)->execute();
```

As well as generic text you can also search within specific fields. This
example will match anything with "pizza" in the title.

```php
$res = $dpla->createSearchQuery()->withTitle('pizza')->withPaging(1, 10)->execute();
```

See searchquery.php for full details of what's supported.

The execute() method will return a results object which has a number of
convenient methods for accessing parts of the search results.

```php
// Get the total number of documents that matched the search query.
$total_count = $res->getTotalCount();

// Get the page details that this results object represents.
$page = $res->getPageNumber();
$per_page = $res->getPerPage();

// Get an array of the documents in this results object.
$docs = $res->getDocuments();
```

It also provides convenience methods for getting the next and previous pages
of results.

```php
$prev_page_res = $res->getPrevPage();
$next_page_res = $res->getNextPage();
```

You can also get the query objects for the current, previous and next pages.

```php
$current_query = $res->getSearchQuery();
$prev_page_query = $res->getPrevPageSearchQuery();
$next_page_query = $res->getNextPageSearchQuery();
```

## License

This code is being put into the public domain so please use it in whatever way
you need. Attribution is encouraged and welcomed but not required.

## Contributing

We welcome contributions in the form of pull requests.

## Who are we?

This code was developed and is maintained by [Stuart Dallas](http://stut.net/) at [3ft9 Ltd](http://3ft9.com/).

The original version was heavily based on [William Karavites' Java wrapper](https://github.com/willkara/DplaJavaWrapper).
