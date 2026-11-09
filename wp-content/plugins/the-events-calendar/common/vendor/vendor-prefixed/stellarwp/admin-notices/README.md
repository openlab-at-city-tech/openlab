# Introduction

Displaying notices within the WordPress admin is a highly common need in plugins. Displaying notices
is not difficult, but it gets tedious when wanting to conditionally display notices based on
conditions such as user capability, the current screen, date range, and so forth.

This library is intended to provide a simple, readable way for developers to conditionally display
standard or highly customized notices within the WordPress admin.

# How to use

## Installation

It's recommended that you install Admin Notices as a project dependency
via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/admin-notices
```

> We _actually_ recommend that this library gets included in your project
> using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a
> typical dependency, so checkout
> our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

## Configuration & Initialization

The `AdminNotices` class can be used to configure the library to work within your system and avoid
conflicts with other plugins. Here's an example of what typical setup may look like:

```php
use StellarWP\AdminNotices\AdminNotices;

AdminNotices::initialize('my_plugin', plugin_dir_url(__FILE__) . 'vendor/stellarwp/admin-notices');
```

The `initialize` method accepts two arguments:

1. A unique identifier for your plugin. This is used to avoid conflicts with other plugins.
2. The URL to the library's assets directory. This is used to enqueue the necessary JS files.

### Service Containers

It is not required to use a service container with this library, however if you are using one and
want it to fit within your system, you can connect your container, which **must** implement the
`Psr\Container\ContainerInterface` interface.

Once connected, your container must provide a concrete instance of the
`StellarWP\AdminNotices\Contracts\NotificationsRegistrarInterface` interface. You can either bind
the `StellarWP\AdminNotices\NotificationsRegistrar` class to the interface, or create your own class
that implements the interface.

```php
$container->set('StellarWP\AdminNotices\Contracts\NotificationsRegistrarInterface', function () {
    return new StellarWP\AdminNotices\NotificationsRegistrar();
});

AdminNotices::setContainer($container);
```

## Displaying Notices

All notices are displayed using the `StellarWP\AdminNotices\AdminNotices` facade. There are a few
methods to manage notices:

### `addNotice($id, $message)`

Adds a notice to the queue to be displayed in the standard WordPress admin notice area.

Parameters:

1. `string $id` - A unique identifier for the notice.
2. `string|callback $message` - The message to display. This can be a string or a callback that
   returns a string

```php
use StellarWP\AdminNotices\AdminNotices;

AdminNotices::show('my_notice', 'This is a notice');
AdminNotices::show('my_notice', function () {
    return 'This is a notice';
});
```

### `removeNotice($id)`

Removes a notice from the queue.

Parameters:

1. `string $id` - The unique identifier for the notice to remove

```php
use StellarWP\AdminNotices\AdminNotices;

AdminNotices::removeNotice('my_notice');
```

### `render(AdminNotice $notice)`

Immediately renders the notice to the screen. This is useful if you want to display the notice in a
non-standard location.

Parameters:

1. `AdminNotice $notice` - The notice to render

```php
use StellarWP\AdminNotices\AdminNotices;

$notice = new AdminNotice('my_notice', 'This is a notice');
AdminNotices::render($notice);
```

## Notice Conditions

At the core of this library is the `AdminNotice` class. This class is used to define the notice and
its conditions. When the `AdminNotices::show()` method is used, it returns a new instance of the
`AdminNotice` class to be fluently configured. For example:

```php
use StellarWP\AdminNotices\AdminNotices;

$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->on('edit.php');
    ->ifUserCan('manage_options')
    ->dismissible()
```

### `on(...$screen)`

Sets the screen where the notice should be displayed. This can be one of:

* A string representing a portion of the URL
* A regex delimited with ~ to compare against the URL (e.g. `~edit\.php~i`)
* An associative array that is compared against the `WP_Screen` object (e.g.
  `['id' => 'edit-post']`)

If multiple screen conditions are provided, the notice will be displayed if any of the conditions
are met.

Parameters:

1. `string|array $screen` - The screen to display the notice on

```php
use StellarWP\AdminNotices\AdminNotices;

// Display the notice where the URL contains 'edit.php'
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->on('edit.php');

// Display the notice where the URL matches the regex
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->on('~edit\.php~i');

// Display the notice on the 'edit-post' screen
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->on(['id' => 'edit-post']);
```

### `ifUserCan(...$capability)`

Sets the capability required to view the notice. This can be a single capability or an array of
capabilities. Under the hood, `current_user_can` is used to check the capability. Each capability
can be one of:

* A string representing a capability
* An array where the elements are spread to the `current_user_can` function
* An instance of the `StellarWP\AdminNotices\ValueObjects\UserCapability` class

If multiple capabilities are provided, the notice will be displayed if the user has
any of the capabilities.

Parameters:

1. `string|array|UserCapability $capability` - The capability required to view the notice

```php
use StellarWP\AdminNotices\AdminNotices;

// Display the notice if the user can manage options
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->ifUserCan('manage_options');

// Display the notice if the user can manage options or edit posts
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->ifUserCan('manage_options', 'edit_posts');

// Display the notice if the user can edit post 1
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->ifUserCan(['edit_post', 1]);

// Display the notice via a UserCapability object
$capability = new StellarWP\AdminNotices\ValueObjects\UserCapability('manage_options');
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->ifUserCan($capability);
```

### `after($date)`

Sets the date after which the notice should be displayed.

Parameters:

1. `string $date` - The date after which the notice should be displayed.

```php
use StellarWP\AdminNotices\AdminNotices;

// Display the notice after January 1, 2022, using a date parsable string
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->after('2022-01-01');

// Display the notice after January 1, 2022, using a DateTime object
$date = new DateTime('2022-01-01');
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->after($date);

// Display the notice after January 1, 2022, using a timestamp
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->after(1640995200);
```

### `until($date)`

Sets the date until which the notice should be displayed.

Parameters:

1. `string $date` - The date until which the notice should be displayed.

```php
use StellarWP\AdminNotices\AdminNotices;

// Display the notice until January 1, 2022, using a date parsable string
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->until('2022-01-01');

// Display the notice until January 1, 2022, using a DateTime object
$date = new DateTime('2022-01-01');
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->until($date);

// Display the notice until January 1, 2022, using a timestamp
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->until(1640995200);
```

### `between($start, $end)`

Sets the date range during which the notice should be displayed. The dates can be the same string,
int, or DateTime object as the `after` and `until` methods.

Parameters:

1. `string $start` - The start date of the range.
2. `string $end` - The end date of the range.

```php
use StellarWP\AdminNotices\AdminNotices;

// Display the notice between January 1, 2022, and January 31, 2022, using date parsable strings
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->between('2022-01-01 00:00:00', '2022-01-31 23:59:59');
```

### `when($callback)`

Sets a custom condition for when the notice should be displayed. The callback should return a
boolean value.

Parameters:

1. `callable $callback` - The callback that returns a boolean value

```php
use StellarWP\AdminNotices\AdminNotices;

// Display the notice if the current user is an administrator
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->when(function () {
        $user = wp_get_current_user();
        return in_array('administrator', $user->roles);
    });
```

## Visual & behavior options

### `autoParagraph($autoParagraph)`, `withoutAutoParagraph()`

**Default:** false

Sets whether the notice message should be automatically wrapped in a paragraph tag. It uses wpautop
under the hood.

Parameters:

1. `bool $autoParagraph = true` - Whether to automatically wrap the message in a paragraph tag

```php
use StellarWP\AdminNotices\AdminNotices;

// Automatically wrap the message in a paragraph tag
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->autoParagraph();

// Do not automatically wrap the message in a paragraph tag
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->autoParagraph(false);

// Also has an alias for readability
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->withoutAutoParagraph();
```

### `withWrapper($with)`, `withoutWrapper()`

**Default:** true

Sets whether the rendered notice should be wrapped in the standard WordPress notice wrapper.

Parameters:

1. `bool $with = true` - Whether to wrap the notice in the standard WordPress notice wrapper

```php
use StellarWP\AdminNotices\AdminNotices;

// Wrap the notice in the standard WordPress notice wrapper
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->withWrapper();

// Do not wrap the notice in the standard WordPress notice wrapper
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->withWrapper(false);

// Also has an alias for readability
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->withoutWrapper();
```

### `urgency($urgency)`

**Default:** 'info'

Sets the urgency of the notice. This is used to determine the color of the notice. **Only works when
the wrapper is enabled.**

Parameters:

1. `string $urgency` - The urgency of the notice. Can be 'info', 'success', 'warning', or 'error'

```php
use StellarWP\AdminNotices\AdminNotices;

// Set the notice urgency to 'success'
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->urgency('success');

// The StellarWP\AdminNotices\ValueObjects\Urgency class can also be used
$urgency = new StellarWP\AdminNotices\ValueObjects\Urgency('success');
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->urgency($urgency);
```

### `alternateStyles($useAlternate)`, `standardStyles()`

**Default:** false

Sets whether the notice should use the alternate WordPress notice styles. **Only works when the
wrapper is enabled.**

Parameters:

1. `bool $useAlternate = true` - Whether the notice should use the alternate WordPress notice
   styles

```php
use StellarWP\AdminNotices\AdminNotices;

// Use the alternate WordPress notice styles
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->alternateStyles();

// Use the standard WordPress notice styles, only necessary to revert back
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->alternateStyles()
    ->standardStyles();
```

### `inline($inline)`, `notInline()`

**Default:** false

Sets whether the notice should be displayed in the WP "inline" location, at the top of the admin
page. **Only works when the wrapper is enabled.**

Parameters:

1. `bool $inline = true` - Whether the notice should be displayed inline

```php
use StellarWP\AdminNotices\AdminNotices;

// Display the notice inline
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->inline();

// Display the notice in the standard location
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->inline(false);

// Also has an alias for readability
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->notInline();
```

### `dismissible($dismissible)`, `notDismissible()`

**Default:** false

Sets whether the notice should be dismissible. This adds a dismiss button to the notice. When the
user dismisses the notice, it is permanently dismissed. This is stored in the user's preference
meta. **Only works when the wrapper is enabled.**

Parameters:

1. `bool $dismissible = true` - Whether the notice should be dismissible

```php
use StellarWP\AdminNotices\AdminNotices;

// Make the notice dismissible
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->dismissible();

// Make the notice not dismissible
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->dismissible(false);

// Also has an alias for readability
$notice = AdminNotices::show('my_notice', 'This is a notice')
    ->notDismissible();
```

## Resetting dismissed notices

For dismissible notices, when the user dismisses the notice, it is permanently dismissed. If you
want
to reset the dismissed notice(s), there are a couple methods available.

### `resetNoticeForUser($notificationId, $userId)`

Reset a specific notification for a user.

Parameters:

1. `string $notificationId` - The unique identifier for the notice
2. `int $userId` - The user ID to reset the notice for

```php
use StellarWP\AdminNotices\AdminNotices;

AdminNotices::resetNoticeForUser('my_notice', get_current_user_id());
```

### `resetAllNoticesForUser($userId)`

Reset all dismissed notices for a user.

Parameters:

1. `int $userId` - The user ID to reset all notices for

```php
use StellarWP\AdminNotices\AdminNotices;

AdminNotices::resetAllNoticesForUser(get_current_user_id());
```
