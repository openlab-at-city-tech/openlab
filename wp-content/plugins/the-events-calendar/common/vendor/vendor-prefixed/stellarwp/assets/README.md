# StellarWP Assets

[![Tests](https://github.com/stellarwp/assets/workflows/Tests/badge.svg)](https://github.com/stellarwp/assets/actions?query=branch%3Amain) [![Static Analysis](https://github.com/stellarwp/assets/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/stellarwp/assets/actions/workflows/static-analysis.yml)

A library for managing asset registration and enqueuing in WordPress.

## Table of contents

* [Installation](#installation)
* [Notes on examples](#notes-on-examples)
* [Configuration](#configuration)
  *[Adding Group Paths](#adding-group-paths)
* [Register and enqueue assets](#register-and-enqueue-assets)
  * [Simple examples](#simple-examples)
    * [A simple registration](#a-simple-registration)
    * [A URL-based asset registration](#a-url-based-asset-registration)
    * [Specifying the version](#specifying-the-version)
	* [Specifying a group path](#specifying-a-group-path)
    * [Specifying the root path](#specifying-the-root-path)
	* [Priority of the Paths](#priority-of-the-paths)
    * [Assets with no file extension](#assets-with-no-file-extension)
    * [Dependencies](#dependencies)
    * [Auto-enqueuing on an action](#auto-enqueuing-on-an-action)
    * [Adding JS and CSS at the same time](#adding-js-and-css-at-the-same-time)
  * [Comprehensive CSS example](#comprehensive-css-example)
  * [Comprehensive JS example](#comprehensive-js-example)
  * [Enqueuing manually](#enqueuing-manually)
    * [Enqueuing a whole group](#enqueuing-a-whole-group)
* [Working with registered `Assets`](#working-with-registered-assets)
  * [`exists()`](#exists)
  * [`get()`](#get)
  * [`remove()`](#remove)
* [Advanced topics](#advanced-topics)
  * [Minified files](#minified-files)
	* [Support for wp-scripts](#support-for-wp-scripts)
	  * [Default example](#default-example)
	  * [Overriding the default asset file location](#overriding-the-default-asset-file-location)
	  * [Specifying translations for a JS asset](#specifying-translations-for-a-js-asset)
  * [Conditional enqueuing](#conditional-enqueuing)
  * [Firing a callback after enqueuing occurs](#firing-a-callback-after-enqueuing-occurs)
  * [Output JS data](#output-js-data)
	  * [Using a callable to provide localization data](#using-a-callable-to-provide-localization-data)
  * [Output content before/after a JS asset is output](#output-content-beforeafter-a-js-asset-is-output)
  * [Style meta data](#style-meta-data)

## Installation

It's recommended that you install Assets as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/assets
```

> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

## Notes on examples

Since the recommendation is to use Strauss to prefix this library's namespaces, all examples will be using the `Boomshakalaka` namespace prefix.

## Configuration

This library requires some configuration before its features can be used. The configuration is done via the `Config` class.

```php
use Boomshakalaka\StellarWP\Assets\Config;

add_action( 'plugins_loaded', function() {
	Config::set_hook_prefix( 'boom-shakalaka' );
	Config::set_path( PATH_TO_YOUR_PROJECT_ROOT );
	Config::set_version( YOU_PROJECT::VERSION );

	// Optionally, set a relative asset path. It defaults to `src/assets/`.
	// This path is where your JS and CSS directories are stored.
	Config::set_relative_asset_path( 'src/assets/' );
} );
```

### Adding Group Paths

Now you can specify "group paths" in your application. This enables you to load assets which are stored in locations outside of your path set through `Config::set_path( PATH_TO_YOUR_PROJECT_ROOT );`

```php
Config::add_group_path( 'group-path-slug', GROUP_PATH_ROOT, 'group/path/relevant/path', true );
```

**Note**: Specifying the 4th parameter of `add_group_path` method as `true`, means that all the assets that belong to the specified `group-path-slug` will have their paths prefixed with `css` or `js`.

For example:

```php
Config::add_group_path( 'group-path-slug', GROUP_PATH_ROOT, 'group/path/relevant/path', true );

Asset::add( 'another-style', 'css/another.css' )
	->add_to_group_path( 'group-path-slug' );

// This asset's would be found in GROUP_PATH_ROOT . 'group/path/relevant/path' . '/css/css/another.css'
```

If you don't want the above to happen you would either specify false or leave the 4th parameter to its default state. Then the asset of the above example would be found in:
`GROUP_PATH_ROOT . 'group/path/relevant/path' . '/css/another.css'`

## Register and enqueue assets

There are a lot of options that are available for handling assets

### Simple examples

For all examples, assume that the following `use` statement is being used:

```php
use Boomshakalaka\StellarWP\Assets\Asset;
```

#### A simple registration

```php
Asset::add( 'my-style', 'css/my-style.css' )
	->register();
```

#### A URL-based asset registration

```php
Asset::add( 'remote-js', 'https://someplace.com/script.js' )
	->register();
```

#### Specifying the version
By default, assets inherit the version of that set in Config::get_version(), but you
can specify a version manually:

```php
Asset::add( 'another-style', 'css/another.css', '1.2.3' )
	->register();
```

#### Specifying a group path
To specify a group path first you need to have it registered. So in a hook prior your asset is being added to the `group-path-slug` you should run:

```php
Config::add_group_path( 'group-path-slug', GROUP_PATH_ROOT, 'group/path/relevant/path' );
```

Then you can specify the above group path in your assets while being added or later.

```php
Asset::add( 'another-style', 'css/another.css' )
	->add_to_group_path( 'group-path-slug' );
```

Now the asset `another-style` would be search inside `GROUP_PATH_ROOT . '/group/path/relevant/path'`

#### Specifying the root path
By default, assets are searched for/found from the root path (unless they belong to a group path) of your project based on
the value set in Config::get_path(), but you can specify a root path manually:

```php
Asset::add( 'another-style', 'css/another.css', null, $my_path )
	->register();
```

#### Priority of the Paths

  1. If a specific root path is set for the asset, that will be used.
  2. If a path group is set for the asset, that will be used.
  3. Otherwise, the root path of the project will be used.

#### Assets with no file extension

If you need to register an asset where the asset does not have an extension,
you can do so by manually setting the asset type, like so:

```php
Asset::add( 'extension-less', 'https://someplace.com/a/style' )
	->set_type( 'css' )
	->register();

// or:

Asset::add( 'extension-less', 'https://someplace.com/a/script' )
	->set_type( 'js' )
	->register();
```

#### Setting priority order

You can set scripts to enqueue in a specific order via the `::set_priority()` method. This method takes an integer and
works similar to the action/filter priorities in WP:

```php
Asset::add( 'my-style', 'css/my-style.css' )
	->set_priority( 20 )
	->register();
```

#### Dependencies
If your asset has dependencies, you can specify those like so:

```php
Asset::add( 'script-with-dependencies', 'js/something.js' )
	->set_dependencies( 'jquery', 'jquery-ui', 'some-other-thing' )
	->register();
```

You can also specify dependencies as a callable that returns an array of dependencies, like so:

```php
Asset::add( 'script-with-dependencies', 'js/something.js' )
	->set_dependencies( function() {
		return [ 'jquery', 'jquery-ui', 'some-other-thing' ];
	} )
	->register();
```

Note that the callable will be executed when the asset is **_enqueued_**.

#### Auto-enqueuing on an action
To specify when to enqueue the asset, you can indicate it like so:

```php
Asset::add( 'yet-another-style', 'css/yet-another.css' )
	->enqueue_on( 'wp_enqueue_scripts' )
	->register();
```

#### Adding JS and CSS at the same time

If you have a JS file and a CSS file that share the same directory and you wish to register them at the same time (typically helpful for [assets built with `wp-scripts`](#support-for-wp-scripts)), you can do this like so:

```php
// Add the JS file and then register_with_css() to get a CSS file at the same time.
Asset::add( 'something-js', 'build/something.js' )
	->enqueue_on( 'wp_enqueue_scripts' )
	->set_dependencies( 'another-js' )
	->register_with_css( 'some-css-dependency', 'another-css-dependency');

// OR

// Add the CSS file and then register_with_js() to get a JS file at the same time.
Asset::add( 'something-css', 'build/something.css' )
	->enqueue_on( 'wp_enqueue_scripts' )
	->set_dependencies( 'another-css' )
	->register_with_js( 'some-js-dependency', 'another-js-dependency' );
```

The following items get cloned over from the original asset:

* `add_to_group()`
* `enqueue_on()`
* `set_condition()`
* `set_min_path()`
* `set_path()`
* Asset slug ( `-style`, `-script`, `-css`, and `-js` are stripped from the end of the original asset slug and replaced with either `-css` or `-js`)
* Version

**Note:** When auto-registering CSS or JS in this way, if there is a `.asset.php` file, the auto-registered asset will _not_ use the `.asset.php` file. If there is an asset file for both, it is best to register each on their own, or to use `::clone_to()`, make some changes, and then call `::use_asset_file( true )` on the cloned asset.

### Comprehensive CSS example

The following example shows all of the options available during the registration of an asset.

```php
use Boomshakalaka\StellarWP\Assets\Asset;

Asset::add( 'my-asset', 'css/some-asset.css', $an_optional_version, $an_optional_path_to_project_root )
	->add_style_data( 'rtl', true )
	->add_style_data( 'suffix', '.rtl' )
	->add_to_group( 'my-assets' ) // You can have more than one of these.
	->call_after_enqueue( // This can be any callable.
		static function() {
			// Do something after the asset is enqueued.
		}
	)
	->enqueue_on( 'wp_enqueue_scripts', 20 ) // The second arg is optional and can be set separately via `::set_priority()`.
	->set_condition( // This can be any callable that returns a boolean.
		static function() {
			return is_front_page() || is_single();
		}
	)
	->set_dependencies( 'some-css' ) // Each dependency becomes a parameter in this method.
	->set_media( 'screen' )
	->set_min_path( 'src/assets/build/' )
	->set_path( 'src/assets' )
	->set_type( 'css' ) // Technically unneeded due to the .js extension.
	->register();
```

### Comprehensive JS example

```php
use Boomshakalaka\StellarWP\Assets\Asset;

Asset::add( 'my-asset', 'js/some-asset.js', $an_optional_version, $an_optional_path_to_project_root )
	->add_localize_script( // You can have more than one of these.
		'some_js_variable',
		[
			'color' => 'blue',
		]
	)
	->add_to_group( 'my-assets' ) // You can have more than one of these.
	->call_after_enqueue( // This can be any callable.
		static function() {
			// Do something after the asset is enqueued.
		}
	)
	->enqueue_on( 'wp_enqueue_scripts', 20 ) // The second arg is optional and can be set separately via `::set_priority()`.
	->print_before( '<b>Before</b>' )
	->print_after( '<b>After</b>' )
	->set_as_async( true )
	->set_as_deferred( true )
	->set_as_module( true )
	->set_asset_file( 'other-asset-directory/some-asset' ) // This allows you to manually set the path to a *.asset.php file.
	->set_condition( // This can be any callable that returns a boolean.
		static function() {
			return is_front_page() || is_single();
		}
	)
	->set_dependencies( 'jquery' ) // Each dependency becomes a parameter in this method.
	->set_min_path( 'src/assets/build/' )
	->set_path( 'src/assets' )
	->set_type( 'js' ) // Technically unneeded due to the .js extension.
	->register();
```

### Enqueuing manually

Sometimes you don't wish to set an asset to enqueue automatically on a specific action. In these cases, you can
trigger a manual enqueue:

```php
use Boomshakalaka\StellarWP\Assets\Assets;

// Enqueue a single asset:
Assets::instance()->get( 'my-style' )->enqueue();

// Enqueue multiple assets:
Assets::instance()->enqueue(
	[
		'my-style',
		'my-script',
		'something-else',
	]
);

/**
 * If you want to force the enqueue to happen and ignore any conditions,
 * you can pass `true` to the second argument.
 */
Assets::instance()->enqueue(
	[
		'my-style',
		'my-script',
		'something-else',
	],
	true
);

// And here's how you can do it with a specific asset:
Assets::instance()->get( 'my-style' )->enqueue( true );
```

#### Enqueuing a whole group

If you have a group of assets that you want to enqueue, you can do so like this:

```php
use Boomshakalaka\StellarWP\Assets\Assets;

// You can do single groups:
Assets::instance()->enqueue_group( 'group-name' );

// or multiple:
Assets::instance()->enqueue_group( [ 'group-one', 'group-two' ] );

// or if you want to force the enqueuing despite conditions:
Assets::instance()->enqueue_group( 'group-name', true );
```

## Working with registered `Assets`

### `exists()`

You can check if an asset has been registered with this library by using the `::exists()` method. This method takes the
the asset slug as an argument and returns a `bool`.

```php
use Boomshakalaka\StellarWP\Assets\Asset;
use Boomshakalaka\StellarWP\Assets\Assets;

Asset::add( 'my-asset', 'js/some-asset.js' )->register();

$assets = Assets::instance();
$assets->exists( 'my-asset' ); // true
$assets->exists( 'another-asset' ); // false
```

### `get()`

You can retrieve an asset object that has been registered by calling the `::get()` method. This method takes the asset
slug as an argument and returns an `Asset` object or `null`.

```php
use Boomshakalaka\StellarWP\Assets\Asset;
use Boomshakalaka\StellarWP\Assets\Assets;

Asset::add( 'my-asset', 'js/some-asset.js' )->register();

$assets    = Assets::instance();
$asset_obj = $assets->get( 'my-asset' );
```

### `remove()`

You can remove an asset from registration and enqueueing by calling the `::remove()` method. This method takes the asset
slug as an argument and returns an `Asset` object or `null`.

```php
use Boomshakalaka\StellarWP\Assets\Asset;
use Boomshakalaka\StellarWP\Assets\Assets;

Asset::add( 'my-asset', 'js/some-asset.js' )->register();

$assets    = Assets::instance();
$assets->get( 'my-asset' )->enqueue();

// This will wp_dequeue_*() the asset and remove it from registration.
$assets->remove( 'my-asset' );
```

## Advanced topics

### Minified files

By default, if you register an asset and `SCRIPT_DEBUG` is not enabled, minified files will dynamically be used if present
in the same directory as the original file. You can, however, specify a different path to look for the minified asset.

The following example will look for `js/some-asset.min.js` in `src/assets/build/` (note the alteration of the file name):

```php
Asset::add( 'my-asset', 'js/some-asset.js' )
	->set_min_path( 'src/assets/build/' )
	->register();
```

### Support for wp-scripts

This library supports `*.asset.php` files generated by `wp-scripts` out of the box. It will attempt to find the `*.asset.php` file in the same directory as the asset file you're registering, however, you can manually set the path to the asset file via `::set_asset_file()` if you need to.

#### Default example

Assume you have a `something.asset.php` file in the same directory as your `something.js` file. Within that asset file is the standard asset array that contains `dependencies` and `version` keys.

```php
Asset::add( 'my-thing', 'js/something.js' )
	->register();
```

This will automatically use the `something.asset.php` file's `dependencies` and `version` values for the asset.

Shown below is an example of the directory structure:

```
my-plugin/
├── src/
│   ├── assets/
│   │   ├── js/
│   │   │   ├── something.js
│   │   │   └── something.asset.php
```

Within the `something.asset.php` file, you have the following:

```php
<?php return array('dependencies' => array('some-dependency'), 'version' => '5.0.0');
```

#### Overriding the default asset file location

You may need to override the default location of the asset file. You can do this by using the `::set_asset_file()` method.

```php
Asset::add( 'my-thing', 'js/something.js' )
	->set_asset_file( 'other-asset-directory/something' )
	->register();
```

Note: You can provide the JS file extension (`other-asset-directory/something.js`), the asset file extension (`other-asset-directory/something.asset.php`), or leave it off entirely (`other-asset-directory/something`).

#### Specifying translations for a JS asset

You can specify translations for a JS asset like so:

```php
// Using the default path of 'languages/'.
Asset::add( 'my-thing', 'js/something.js' )
	->with_translations( $textdomain )
	->register();

// Specifying a different path.
Asset::add( 'my-thing', 'js/something.js' )
	->with_translations( $textdomain, 'relative/path/to/json/lang/files' )
	->register();

// Using the 'default' textdomain and the default path of 'languages/'.
Asset::add( 'my-thing', 'js/something.js' )
	->with_translations()
	->register();
```

### Conditional enqueuing

It is rare that you will want to enqueue an asset on every page load. Luckily, you can specify a condition for when an
asset should be enqueued using the `::set_condition()` method. This method takes a callable that should return a boolean
that represents whether the asset should be enqueued or not.

```php
use Boomshakalaka\StellarWP\Assets\Asset;

// Simple condition.
Asset::add( 'my-asset', 'css/some-asset.css' )
	->set_condition( 'is_single' )
	->register();

// Class-based method.
Asset::add( 'my-asset', 'css/some-asset.css' )
	->set_condition( [ $my_class, 'my_method_that_returns_boolean' ] )
	->register();

// Anonymous function.
Asset::add( 'my-asset', 'css/some-asset.css' )
	->set_condition( static function() {
		// You can do whatever you want here as long as it returns a boolean!
		return is_single() || is_home();
	} )
	->register();
```

### Firing a callback after enqueuing occurs

Sometimes you need to know when enqueuing happens. You can specify a callback to be fired once enequeuing occurs using
the `::call_after_enqueue()` method. Like the `::set_condition()` method, this method takes a callable.

```php
use Boomshakalaka\StellarWP\Assets\Asset;

// Simple function execution.
Asset::add( 'my-asset', 'css/some-asset.css' )
	->call_after_enqueue( 'do_some_global_function' )
	->register();

// Class-based method.
Asset::add( 'my-asset', 'css/some-asset.css' )
	->call_after_enqueue( [ $my_class, 'my_callback' ] )
	->register();

// Anonymous function.
Asset::add( 'my-asset', 'css/some-asset.css' )
	->call_after_enqueue( static function() {
		// Do whatever in here.
	} )
	->register();
```

### Output JS data

If you wish to output JS data to the page after enqueuing (similar to `wp_localize_script()`), you can make use of the
`::add_localize_script()` method. This method takes two arguments: the first is the name of the JS variable to be
output and the second argument is the data to be assigned to the JS variable. You can chain this method as many times
as you wish!

```php
use Boomshakalaka\StellarWP\Assets\Asset;

Asset::add( 'my-asset', 'css/some-asset.css' )
	->add_localize_script(
		'boomshakalaka_animal',
		[
			'animal' => 'cat',
			'color'  => 'orange',
		]
	)
	->add_localize_script(
		'boomshakalaka_food',
		[
			'breakfast' => 'eggs',
			'lunch'     => 'sandwich',
			'dinner'    => 'enchiladas',
		]
	)
	->register();
```

If you specify an object name using dot notation, then the object will be printed on the page "merging" it with other, pre-existing objects.
In the following example, the `boomshakalaka.project` object will be created and then the `firstScriptData` and `secondScriptData` objects will be added to it:

```php
use Boomshakalaka\StellarWP\Assets\Asset;

Asset::add( 'my-first-script', 'js/first-script.js' )
	->add_localize_script(
		'boomshakalaka.project.firstScriptData',
		[
			'animal' => 'cat',
			'color'  => 'orange',
		]
	)
	->register();

Asset::add( 'my-second-script', 'js/second-script.js' )
	->add_localize_script(
		'boomshakalaka.project.secondScriptData',
		[
			'animal' => 'dog',
			'color'  => 'green',
		]
	)
	->register();

Asset::add( 'my-second-script-mod', 'js/second-script-mod.js' )
	->add_localize_script(
		'boomshakalaka.project.secondScriptData',
		[
			'animal' => 'horse'
		]
	)
	->register();
```

The resulting output will be:

```html
<script id="my-first-script-ns-extra">
	window.boomshakalaka = window.boomshakalaka || {};
	window.boomshakalaka.project = window.boomshakalaka.project || {};
	window.boomshakalaka.project.firstScriptData = Object.assign(
		window.boomshakalaka.project.firstScriptData || {},
		{ "animal": "cat", "color": "orange" }
	);
</script>
<script src="https://someplace.com/wp-content/plugins/my-plugins/js/first-script.js" id="my-first-script-js"></script>
<script id="my-second-script-ns-extra">
	window.boomshakalaka = window.boomshakalaka || {};
	window.boomshakalaka.project = window.boomshakalaka.project || {};
	window.boomshakalaka.project.secondScriptData = Object.assign(
		window.boomshakalaka.project.secondScriptData || {},
		{ "animal": "dog", "color": "green" }
	);
</script>
<script src="https://someplace.com/wp-content/plugins/my-plugins/js/second-script.js" id="my-second-script-js"></script>
<script id="my-second-script-mod-ns-extra">
	window.boomshakalaka = window.boomshakalaka || {};
	window.boomshakalaka.project = window.boomshakalaka.project || {};
	window.boomshakalaka.project.secondScriptData = Object.assign(
		window.boomshakalaka.project.secondScriptData || {},
		{ "animal": "horse" }
	);
</script>
<script src="https://someplace.com/wp-content/plugins/my-plugins/js/second-script-mod.js"
		id="my-second-script-mod-js"></script>
```

Note the `my-second-script-mod` handle is overriding a specific nested
key, `boomshakalaka.project.secondScriptData.animal`, in the `boomshakalaka.project.secondScriptData` object while
preserving the other keys.

#### Using a callable to provide localization data

If you need to provide localization data dynamically, you can use a callable to do so. The callable will be called
when the asset is enqueued and the return value will be used. The callable will be passed the asset as the first
argument and should return an array.

```php
Asset::add( 'my-script', 'js/some-asset.js' )
	->add_localize_script(
		'boomshakalaka.project.myScriptData',
		function( Asset $asset ) {
			return [
				'animal' => 'cat',
				'color'  => 'orange',
			];
		}
	)
	->register();
```

Any valid callable can be used, including Closures, like in the example above.

### Output content before/after a JS asset is output

There may be times when you wish to output markup or text immediately before or immediately after outputting the JS
asset. You can make use of `::print_before()` and `::print_after()` to do this.

```php
use Boomshakalaka\StellarWP\Assets\Asset;

Asset::add( 'my-asset', 'js/some-asset.js' )
	->print_before( '<b>Before</b>' )
	->print_after( '<b>After</b>' )
	->register();
```

### Style meta data

Assets support adding meta data to stylesheets. This is done via the `::add_style_data()` method. This method takes two
arguments: the first is the name of the meta data and the second is the value of the meta data. You can chain this and
call this method multiple times.

This works similar to the [`wp_style_add_data()`](https://developer.wordpress.org/reference/functions/wp_style_add_data/) function.

```php
use Boomshakalaka\StellarWP\Assets\Asset;

Asset::add( 'my-asset', 'css/some-asset.css' )
	->add_style_data( 'rtl', true )
	->add_style_data( 'suffix', '.rtl' )
	->register();
```
