# Telemetry Library
![Tests](https://github.com/stellarwp/telemetry/actions/workflows/tests.yml/badge.svg)
![Coding Standards](https://github.com/stellarwp/telemetry/actions/workflows/phpcs.yml/badge.svg)
![PHP Compatibility](https://github.com/stellarwp/telemetry/actions/workflows/compatibility.yml/badge.svg)
![Static Analysis](https://github.com/stellarwp/telemetry/actions/workflows/phpstan.yml/badge.svg)

A library for Opt-in and Telemetry data to be sent to the StellarWP Telemetry server.

## Table of Contents
- [Telemetry Library](#telemetry-library)
	- [Table of Contents](#table-of-contents)
	- [Installation](#installation)
	- [Usage Prerequisites](#usage-prerequisites)
	- [Filters \& Actions](#filters--actions)
	- [Integration](#integration)
	- [Uninstall Hook](#uninstall-hook)
	- [Opt-In Modal Usage](#opt-in-modal-usage)
		- [Prompting Users on a Settings Page](#prompting-users-on-a-settings-page)
	- [Saving Opt-In Status on a Settings Page](#saving-opt-in-status-on-a-settings-page)
	- [How to Migrate Users Who Have Already Opted In](#how-to-migrate-users-who-have-already-opted-in)
	- [Utilizing a Shared Telemetry Instance](#utilizing-a-shared-telemetry-instance)
	- [Adding Plugin Data to Site Health](#adding-plugin-data-to-site-health)
	- [Capturing User Events](#capturing-user-events)
	- [Contribution](#contribution)
## Installation

It's recommended that you install Telemetry as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/telemetry
```


> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

## Usage Prerequisites
To actually _use_ the telemetry library, you must have a Dependency Injection Container (DI Container) that is compatible with the [StellarWP Container Contract](https://github.com/stellarwp/container-contract).

In order to keep this library as light as possible, a container is not included in the library itself, however we do recommend [di52](https://github.com/lucatume/di52). To avoid version compatibility issues, it is also not included as a Composer dependency. Instead, you must include it in your project. We recommend including it via composer [using Strauss](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md), just like you have done with this library.

## Filters & Actions
If you'd like to take a look at the existing filters & actions available through the library, [view that documentation here](docs/filters.md).
## Integration
Initialize the library within your main plugin file after plugins are loaded (or anywhere else you see fit). You can configure a unique prefix (we suggest you use your plugin slug) so that hooks can be uniquely called for your specific instance of the library.

```php
use StellarWP\Telemetry\Core as Telemetry;

add_action( 'plugins_loaded', 'initialize_telemetry' );

function initialize_telemetry() {
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

	// Set the full URL for the Telemetry Server API.
	Config::set_server_url( 'https://telemetry.example.com/api/v1' );

	// Set a unique prefix for actions & filters.
	Config::set_hook_prefix( 'my-custom-prefix' );

	// Set a unique plugin slug.
	Config::set_stellar_slug( 'my-custom-stellar-slug' );

    // Initialize the library.
    Telemetry::instance()->init( __FILE__ );
}
```

Using a custom hook prefix provides the ability to uniquely filter functionality of your plugin's specific instance of the library.

The unique plugin slug is used by the telemetry server to identify the plugin regardless of the plugin's directory structure or slug.

If you need to hook into an existing instance of the library, you can add your plugin's stellar slug with:
```php
add_action( 'plugins_loaded', 'hook_into_existing_telemetry' );

function hook_into_existing_telemetry() {
	// Check to make sure that the Telemetry library is already instantiated.
	if ( ! class_exists( Telemetry::class ) ) {
		return;
	}

	// Register the current plugin with an already instantiated library.
	Config::add_stellar_slug( 'my-custom-stellar-slug', 'custom-plugin/custom-plugin.php' );
}
```

## Uninstall Hook

This library provides everything necessary to uninstall itself. Depending on when your plugin uninstalls itself and cleans up the database, you can include this static method to have the library purge the options table of the necessary rows:
```php
<?php// uninstall.php

use YOUR_STRAUSS_PREFIX\StellarWP\Telemetry\Uninstall;

require_once 'vendor/strauss/autoload.php';

Uninstall::run( 'my-custom-stellar-slug' );
```
When a user deletes the plugin, WordPress runs the method from `Uninstall` and cleans up the options table. The last plugin utilizing the library will remove all options.

## Opt-In Modal Usage

![The default modal shown to users that have not dismissed it yet.](docs/img/default-opt-in-modal.png)

### Prompting Users on a Settings Page

On each settings page you'd like to prompt the user to opt-in, add a `do_action()`. _Be sure to include your defined stellar\_slug_.
```php
do_action( 'stellarwp/telemetry/optin', '{stellar_slug}' );
```

Or, if you're implementing the library prior to version 3.0.0:
```php
/**
 * Planned Deprecation: 3.0.0
 *
 * Please use 'stellarwp/telemetry/optin' action instead.
 */
do_action( 'stellarwp/telemetry/{stellar_slug}/optin' );
```
The library calls this action to handle registering the required resources needed to render the modal. It will only display the modal for users who haven't yet opted in.

To show the modal on a settings page, add the `do_action()` to the top of your rendered page content:
```php
function my_options_page() {
    do_action( 'stellarwp/telemetry/optin', '{stellar_slug}' );
    ?>
    <div>
        <h2>My Plugin Settings Page</h2>
    </div>
    <?php
}
```
_Note: When adding the `do_action`, you may pass additional arguments to the library with an array. There is no functionality at the moment, but we expect to expand the library to accept configuration through the passed array._
```php
do_action( 'stellarwp/telemetry/optin', '{stellar_slug}', [ 'plugin_slug' => 'the-events-calendar' ] );
```

## Saving Opt-In Status on a Settings Page
When implementing the library, settings should be available for site administrators to change their opt-in status at any time. The value passed to `set_status()` should be a boolean.

```php
add_action( 'admin_init', 'save_opt_in_setting_field' );

/**
 * Saves the "Opt In Status" setting.
 *
 * @return void
 */
public function save_opt_in_setting_field() {
	// Return early if not saving the Opt In Status field.
	if ( ! isset( $_POST[ 'opt-in-status' ] ) ) {
		return;
	}

	// Get an instance of the Status class.
	$Status = Config::get_container()->get( Status::class );

	// Get the value submitted on the settings page as a boolean.
	$value = filter_input( INPUT_POST, 'opt-in-status', FILTER_VALIDATE_BOOL );

	$Status->set_status( $value );
}
```

## How to Migrate Users Who Have Already Opted In
If you have a system that users have already opted in to and you'd prefer not to have them opt in again, here's how you might go about it. The `opt_in()` method will set their opt-in status to `true` and send their telemetry data and user data to the telemetry server.

```php
/**
 * The library attempts to set the opt-in status for a site during 'admin_init'. Use the hook with a priority higher
 * than 10 to make sure you're setting the status after it initializes the option in the options table.
 */
add_action( 'admin_init', 'migrate_existing_opt_in', 11 );

function migrate_existing_opt_in() {

	if ( $user_has_opted_in_already ) {

		// Get the Opt_In_Subscriber object.
		$Opt_In_Subscriber = Config::get_container()->get( Opt_In_Subscriber::class );
		$Opt_In_Subscriber->opt_in();
	}
}
```

## Utilizing a Shared Telemetry Instance

There are cases where a plugin may want to use a shared instance of the library.

The best example of this would be an add-on plugin connecting and using the Telemetry instance of its parent plugin.

In this case, all that the plugin needs to do to implement the parent Telemetry instance is use `Config::add_stellar_slug()`.
```php
use STRAUSS_PREFIX\StellarWP\Telemetry\Config;

add_action( 'plugins_loaded', 'add_plugin_to_telemetry' );

function add_plugin_to_telemetry() {

	// Verify that Telemetry is available.
	if ( ! class_exists( Config::class ) ) {
        return;
    }

	// Set a unique plugin slug and include the plugin basename.
	Config::add_stellar_slug( 'my-custom-stellar-slug', 'my-custom-stellar-slug/my-custom-stellar-slug.php' );
}
```

## Adding Plugin Data to Site Health

We collect the Site Health data as json on the server.  In order to pass additional plugin specific items that can be reported on, you will need to add a section to the Site Health Data. The process for adding a section is documented on [developer.wordpress.org](https://developer.wordpress.org/reference/hooks/debug_information/).

We do have some requirements so that we can grab the correct data from Site Health. When setting the key to the plugins site health section, use the plugin slug. Do not nest your settings in a single line, use one line per option. Do not translate the debug value. This will help make sure that the data is reportable on the Telemetry Server.

``` php
function add_summary_to_telemtry( $info ) {
	$info[ 'stellarwp' ] = [
			'label'       => esc_html__( 'StellarWP Plugin Section', 'text-domain' ),
			'description' => esc_html__( 'There are some key things here... Everything should be output in key value pairs. Follow the translation instructions in the codex (do not translate debug). Plugin Slug should be the main key.', 'text-domain' ),
			'fields'      => [
				'field_key_one' => [
					'label' => esc_html__( 'This is the field text', 'text-domain' ),
					'value' => esc_html__( 'value', 'text-domain' ),
					'debug' => 'value'
				],
				'field_key_two' => [
					'label' => esc_html__( 'Field Two', 'text-domain' ),
					'value' => esc_html__( 'yes', 'text-domain' ),
					'debug' => true,
				],
				'field_key_three' => [
					'label' => esc_html__( 'Three', 'text-domain' ),
					'value' => esc_html__( 'Tempus pellentesque id hac', 'text-domain' ),
					'debug' => 'Tempus pellentesque id hac',
				],
				'field_key_four' => [
					'label' => esc_html__( 'Option Four', 'text-domain' ),
					'value' => esc_html__( 'on', 'text-domain' ),
					'debug' => true,
				],
			],
		];
	return $info;
}

add_filter( 'debug_information', 'add_summary_to_telemetry', 10, 1) ;
```

## Capturing User Events

When a user completes an action, an event can be captured with the telemetry server for a specific site. These events take `name` and `data` (array) parameters to capture any specific information necessary for the event.

Some examples of actions you may want to capture:
- User creates their first post
- A plugin feature is used for the first time (but not completed or utilized)
- X days have passed and a feature has not yet been utilized

**NOTE: All plugins should trigger an event when a user opts out of telemetry for a site.**

To create an event, set up a do_action with the necessary details wherever you'd like to capture it:
```php
// Event data is sent to the telemetry server as JSON.
$data = [
	'one'   => 1,
	'two'   => 2,
	'three' => 3,
];
do_action( 'stellarwp/telemetry/{hook-prefix}/event', 'your-event-name', $data );
```

Here is how you might log events when a user creates a new post:
```php
/**
 * Log event when a user creates a new post.
 *
 * @action save_post
 *
 * @param int     $post_id The ID of the post being saved.
 * @param WP_Post $post    The post object being saved.
 * @param bool    $update  If this is an update to a pre-existing post.
 *
 * @return void
 */
function user_creates_post( $post_id, $post, $update ) {
	// Only send events for new posts.
	if ( $update ) {
		return;
	}

	// Only send event for posts, avoid everything else.
	if ( $post->post_type !== 'post' ) {
		return;
	}

	// Add any data to the event that needs to be captured.
	$event_data = [
		'title'   => $post->post_title,
		'content' => $post->post_content,
		'some-other-data' => 'use the array to capture anything else that might be necessary for context'
	];

	// Log the event with the telemetry server.
	do_action( 'stellarwp/telemetry/{hook-prefix}/event', 'new_post', $event_data );
}
```

## Contribution

There are more detailed docs that provide guidance on contributing to the library:
- [Automated testing](/docs/automated-testing.md)
- [Local Environment Configuration](/docs/local-environment.md)
- [Quality Assurance](/docs/quality-assurance.md)
