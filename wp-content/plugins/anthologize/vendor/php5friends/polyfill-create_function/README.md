# Polyfill of `create_function()`

Uncover the grave of [`create_function()`].

Anonymous functions from this function were introduced in PHP4, obsolete with the introduction of closures in PHP5, and finally purged in PHP8.

## Installation

```
composer require php5friends/polyfill-create_function
```

## Security issue

This package uses eval to generate functions. The caller user of the function is responsible for not executing unexpected code.

**In particular, generating a function using external input or a string stored in the Database poses a significant risk.**

## Functions

This package provides `Php5Friends\create_function()` and `create_function()`

### `Php5Friends\create_function()`

Wrapper function of [`create_function()`].  Provides a function generation mechanism for environments where `create_function()` function does not exist.

### `Php5Friends\create_closure()`

Create a `Closure` from same paremeters of `create_function()`.

### `create_function()` for PHP 8

Simply Polyfill function of [`create_function()`].

[`create_function()`]: https://www.php.net/create_function

## Copyright

> (C) Copyright 2020 Friends of PHP5
>
> Copying and distribution of this file, with or without modification,
> are permitted in any medium without royalty provided the copyright
> notice and this notice are preserved.  This file is offered as-is,
> without any warranty.

