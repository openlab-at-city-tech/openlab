# BuddyPress 12.0 URL Polyfills

In version 12.0, BuddyPress changed the way that its URLs are built and routed. Previously, URLs were built using a combination of WordPress page routing (the "bp_pages" system for top-level components), PHP constants, and hardcoded strings. After version 12.0, all URLs run through WP's rewrite API.

To accommodate this breaking change, new functions were introduced in BP 12.0 to build URLs. For example, where previously you might have used `bp_get_group_permalink( $group_id )`, you now use `bp_get_group_url( $group_id )`.

The BuddyPress 12.0 URL Polyfills library allows developers to build BuddyPress plugins that are compatible with both the new and old URL systems. Developers should write code that uses the new URL functions, such as `bp_get_group_url()`. Then, if the plugin is loaded on a site running a version of BuddyPress prior to 12.0, this library will provide the necessary polyfills so that your plugin runs as expected. Similarly, developers updating their existing BuddyPress plugins for BP 12.0 compatibility can update their code to use the new URL functions exclusively, and then use this library to provide backwards compatibility for older versions of BuddyPress.

## Installation

This library is available on Packagist. To install it, run:

```
$ composer require hard-g/buddypress-12.0-url-polyfills
```

Alternatively, you may download the library and install it manually.

## Usage

In your plugin or theme, require the autoloader, and then load the polyfill library as follows:

```php
require_once __DIR__ . '/vendor/autoload.php';

HardG\BuddyPress120URLPolyfills\Loader::init();
```

The polyfill library loads its files on the `'bp_include'` hook. For this reason, it's important that you invoke the library (using the `Loader::init()` method shown above) early in your plugin bootstrap.

## Help!

This library has been built as part of my own efforts to upgrade my many BuddyPress plugins for BP 12.0. As such, it includes polyfills for those functions that I've identified as necessary for my own plugins. If you find more functions that need polyfills, please open a pull request.
