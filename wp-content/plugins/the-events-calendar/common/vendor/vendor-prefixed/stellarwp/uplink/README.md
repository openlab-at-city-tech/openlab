# StellarWP Uplink

[![CI](https://github.com/stellarwp/uplink/workflows/CI/badge.svg)](https://github.com/stellarwp/uplink/actions?query=branch%3Amain) [![Static Analysis](https://github.com/stellarwp/uplink/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/stellarwp/uplink/actions/workflows/static-analysis.yml)

## Installation

It's recommended that you install Uplink as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/uplink
```

> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

## Initialize the library

Initializing the StellarWP Uplink library should be done within the `plugins_loaded` action, preferably at priority `0`.

```php
use StellarWP\Uplink\Uplink;

add_action( 'plugins_loaded', function() {
	/**
	 * Configure the container.
	 *
	 * The container must be compatible with stellarwp/container-contract.
	 * See here: https://github.com/stellarwp/container-contract#usage.
	 *
	 * If you do not have a container, we recommend https://github.com/lucatume/di52
	 * and the corresponding wrapper:
	 * https://github.com/stellarwp/container-contract/blob/main/examples/di52/Container.php
	 */
	$container = new Container();
	Config::set_container( $container );
	Config::set_hook_prefix( 'my-custom-prefix' );

	/*
	 * If you wish to allow a customer to authorize their product, set your Token Auth Prefix.
	 *
	 * This will allow storage of a unique token associated with the customer's license/domain.
	 *
	 * Important: The Token auth prefix should be the same across all of your products.
	 */
	Config::set_token_auth_prefix( 'my_origin' );

	// Optionally, change the default auth token caching.
	Config::set_auth_cache_expiration( WEEK_IN_SECONDS );

	// Or, disable it completely.
	Config::set_auth_cache_expiration( -1 );

	Uplink::init();
}, 0 );
```

## Translation

Package is using `__( 'Invalid request: nonce field is expired. Please try again.', '%TEXTDOMAIN%' )` function for translation. In order to change domain placeholder `'%TEXTDOMAIN%'` to your plugin translation domain run
```bash
./bin/stellar-uplink domain=<your-plugin-domain>
```
or
```bash
./bin/stellar-uplink
```
and prompt the plugin domain
You can also add lines below to your composer file in order to run command automatically
```json
"scripts": {
	"stellar-uplink": [
	  "vendor/bin/stellar-uplink domain=<your-plugin-domain>"
	],
	"post-install-cmd": [
	  "@stellar-uplink"
	],
	"post-update-cmd": [
	  "@stellar-uplink"
	]
  }
```
## Embedding a license in your plugin

StellarWP Uplink plugins are downloaded with an embedded license key so that users do not need to manually enter the key when activating their plugin. To make this possible, the class must be in a specific location so that the licensing server can find it.

```bash
# The class file should be in this path:
src/Uplink/Helper.php
```

The file should match the following - keeping the `KEY` constant set to a blank string, or, if you want a default license key, set it to that.:

```php
<?php declare( strict_types=1 );

namespace Whatever\Namespace\Uplink;

final class Helper {
	public const KEY = '';
}
```

## Registering a plugin

Registers a plugin for licensing and updates.

```php
use StellarWP\Uplink\Register;

$plugin_slug    = 'my-plugin';
$plugin_name    = 'My Plugin';
$plugin_version = MyPlugin::VERSION;
$plugin_path    = 'my-plugin/my-plugin.php';
$plugin_class   = MyPlugin::class;
$license_class  = MyPlugin\Uplink\Helper::class;

Register::plugin(
	$plugin_slug,
	$plugin_name,
	$plugin_version,
	$plugin_path,
	$plugin_class,
	$license_class, // This is optional.
	false // Whether this is an oAuth plugin. Default false.
);
```

## Registering a service

Registers a service for licensing. Since services require a plugin, we pull version and class information from the plugin.

```php
use StellarWP\Uplink\Register;

$service_slug    = 'my-service';
$service_name    = 'My Service';
$service_version = MyPlugin::VERSION;
$plugin_path     = 'my-plugin/my-plugin.php';
$plugin_class    = MyPlugin::class;

Register::service(
	$service_slug,
	$service_name,
	$service_version,
	$plugin_path,
	$plugin_class,
	null,
	false
);
```

## Render license key form on your settings page

In order to render license key form just add the following to your settings page, tab, etc.

> ⚠️ This will render license key fields for all of your registered plugins/services in the same Uplink/Container instance.

```php
use StellarWP\Uplink as UplinkNamespace;

$form = UplinkNamespace\get_form();
$plugins = UplinkNamespace\get_plugins();

foreach ( $plugins as $plugin ) {
	$field = UplinkNamespace\get_field( $plugin->get_slug() );
	// Tha name property of the input field.
	$field->set_field_name( 'field-' . $slug );
	$form->add_field( $field );
}

$form->render();
// or echo $form->get_render_html();
```

To render a single product's license key, use the following:

```php
use StellarWP\Uplink as UplinkNamespace;

$field = UplinkNamespace\get_field( 'my-test-plugin' );

$field->render();
// or echo $field->get_render_html();


```

### Example: Register settings page and render license fields
Register a settings page for a plugin if you need it
```php
add_action( 'admin_menu', function () {
    add_menu_page(
        'Sample',
        'Sample',
        'manage_options',
        'sample-plugin-lib',
        'render_settings_page',
        '',
        null
    );

}, 11 );
```

Add lines below to your settings page. This will render license key form with titles and a submit button
```php
use StellarWP\Uplink as UplinkNamespace;

function render_settings_page() {
    // ...
	$form = UplinkNamespace\get_form();
	$plugins = UplinkNamespace\get_plugins();

	foreach ( $plugins as $plugin ) {
		$field = UplinkNamespace\get_field( $plugin->get_slug() );
		// Tha name property of the input field.
		$field->set_field_name( 'field-' . $slug );
		$form->add_field( $field );
	}

	$form->show_button( true, __( 'Submit', 'text-domain' ) );

	$form->render();

    //....
}
```

## License Authorization

> ⚠️ Your `auth_url` is set on the Origins table on the [Stellar Licensing](https://github.com/stellarwp/licensing) server!
> You must first request to have this added before proceeding.

There may be certain functionality you wish to make available when you know a license is authorized.

This library provides the tools to fetch and store unique tokens, working together with the Uplink Origin
plugin.

After following the instructions at the top to define a `Config::set_token_auth_prefix()`, this will enable the following
functionality:

1. The ability to render a "Connect" button anywhere in your plugin while the user is in wp-admin, using the provided function below.
2. The button will display "Disconnect" once they are authorized, which deletes the locally stored Token.
3. The ability for the customer's site to accept specific Query Variables in wp-admin, that will store the generated Token, and an optional new License Key for a Product Slug.
4. Check if a license is authorized, either in the License Validation payload, or manually.

> ⚠️ Generating a Token requires manual configuration on your Origin site using the [Uplink Origin Plugin](https://github.com/stellarwp/uplink-origin).

### Render Authorize Button

> 💡 Note: the button is only rendered if the following conditions are met:

1. You have an `auth_url` set on the StellarWP Licensing Server.
2. The current user is a Super Admin (can be changed with a WP filter).
3. This is not a multisite installation, or...
4. If multisite and using subfolders, only on the root network dashboard.
5. If multisite and NOT using subfolders and on a subsite AND a token doesn't already exist at the network level, in which case it needs to be managed at the network.

```php
// Call the namespaced function with your plugin slug.
\StellarWP\Uplink\render_authorize_button( 'kadence-blocks-pro' );
```

You can also pass in a custom license domain, which can be fetched on the Uplink Origin side from the `uplink_domain` query variable:

```php
// Call the namespaced function with your plugin slug and license domain.
\StellarWP\Uplink\render_authorize_button( 'kadence-blocks-pro', 'customer-site.com' );
```

> 💡 The button is very customizable with filters, see [Authorize_Button_Controller.php](src/Uplink/Components/Admin/Authorize_Button_Controller.php).

### Manually Check if a License is Remotely Authorized

This connects to the licensing server to check in real time if the license is authorized. Use sparingly.

```php
$token       = \StellarWP\Uplink\get_authorization_token( 'my-plugin-slug' );
$license_key = \StellarWP\Uplink\get_license_key( 'my-plugin-slug' );
$domain      = \StellarWP\Uplink\get_license_domain();

if ( ! $token || ! $license_key || ! $domain ) {
	return; // or, log/show errors.
}

$is_authorized = \StellarWP\Uplink\is_authorized( $license_key, 'my-plugin-slug', $token, $domain );

echo $is_authorized ? esc_html__( 'authorized' ) : esc_html__( 'not authorized' );
```

### Manually Fetch Auth URL

If for some reason you need to fetch your `auth_url` manually, you can do so by:

```php
echo esc_url( \StellarWP\Uplink\get_auth_url( 'my-plugin-slug' ) );
```

> 💡 Auth URL connections are cached for one day using transients.


### Callback Redirect

The Callback Redirect generated by the Origin looks something like this, where `uplinksample.lndo.site` is your
customer's website:

```
https://uplinksample.lndo.site/wp-admin/import.php?uplink_token=d9a407d0-0eb1-41cf-8cd0-e5da668143b4&_uplink_nonce=Oyj13TCvhaa12IJm
```

The Origin is responsible for asking StellarWP Licensing to generate a token and redirect back to where the customer originally
clicked on the button.

The following Query Variables are available for reference:

> 💡 Note: This data automatically gets stored when detected, using the `admin_init` hook!

1. `uplink_token` - The unique UUIDv4 token generated by StellarWP Licensing.
2. `_uplink_nonce` - The original nonce sent with the callback URL, as part of the "Connect" button.
3. `uplink_license` (optional) - Whether we should also update or set a License Key.
4. `uplink_slug` (optional) - The Product or Service Slug that we're updating the license for.

> ⚠️ `uplink_slug` MUST be supplied if `uplink_license` is!
