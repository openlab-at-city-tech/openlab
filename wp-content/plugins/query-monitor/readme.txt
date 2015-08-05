=== Query Monitor ===
Contributors: johnbillion
Tags: debug, debug-bar, debugging, development, developer, performance, profiler, profiling, queries, query monitor
Requires at least: 3.5
Tested up to: 4.2
Stable tag: 2.7.4
License: GPLv2 or later

View debugging and performance information on database queries, hooks, conditionals, HTTP requests, redirects and more.

== Description ==

Query Monitor is a debugging plugin for anyone developing with WordPress. It has some advanced features not available in other debugging plugins, including automatic AJAX debugging and the ability to narrow down things by plugin or theme.

For complete information, please see [Query Monitor's GitHub repo](https://github.com/johnbillion/query-monitor).

Here's an overview of what's shown:

= Database Queries =

 * Shows all database queries performed on the current page
 * Shows **affected rows** and time for all queries
 * Show notifications for **slow queries** and **queries with errors**
 * Filter queries by **query type** (`SELECT`, `UPDATE`, `DELETE`, etc)
 * Filter queries by **component** (WordPress core, Plugin X, Plugin Y, theme)
 * Filter queries by **calling function**
 * View **aggregate query information** grouped by component, calling function, and type
 * Super advanced: Supports **multiple instances of wpdb** on one page

Filtering queries by component or calling function makes it easy to see which plugins, themes, or functions on your site are making the most (or the slowest) database queries.

= Hooks =

 * Shows all hooks fired on the current page, along with hooked actions, their priorities, and their components
 * Filter hooks by **part of their name**
 * Filter actions by **component** (WordPress core, Plugin X, Plugin Y, theme)

= Theme =

 * Shows the **template filename** for the current page
 * Shows the available **body classes** for the current page
 * Shows the active parent and child theme name

= PHP Errors =

 * PHP errors (warnings, notices, stricts, and deprecated) are presented nicely along with their component and call stack
 * Shows an easily visible warning in the admin toolbar

= Request =

 * Shows **matched rewrite rules** and associated query strings
 * Shows **query vars** for the current request, and highlights **custom query vars**
 * Shows the **queried object** details (collapsed by default)
 * Shows details of the **current blog** (multisite only) and **current site** (multi-network only)

= Scripts & Styles =

 * Shows all **enqueued scripts and styles** on the current page, along with their URL and version
 * Shows their **dependencies and dependents**, and alerts you to any **broken dependencies**

= HTTP Requests =

 * Shows all HTTP requests performed on the current page (as long as they use WordPress' HTTP API)
 * Shows the response code, call stack, transport, timeout, and time taken
 * Highlights **erroneous responses**, such as failed requests and anything without a `200` response code

= Redirects =

 * Whenever a redirect occurs, Query Monitor adds an `X-QM-Redirect` HTTP header containing the call stack, so you can use your favourite HTTP inspector to easily trace where a redirect has come from

= AJAX =

The response from any jQuery AJAX request on the page will contain various debugging information in its header that gets output to the developer console. **No hooking required**.

AJAX debugging is in its early stages. Currently it only includes PHP errors, but this will be built upon in future versions.

= Admin Screen =

Hands up who can remember the correct names for the filters and actions for custom admin screen columns?

 * Shows the correct names for **custom column filters and actions** on all admin screens that have a listing table
 * Shows the state of `get_current_screen()` and a few variables

= Environment Information =

 * Shows **various PHP information** such as memory limit and error reporting levels
 * Highlights the fact when any of these are overridden at runtime
 * Shows **various MySQL information**, including caching and performance related configuration
 * Highlights the fact when any performance related configurations are not optimal
 * Shows various details about **WordPress** and the **web server**
 * Shows version numbers for all the things

= Everything Else =

 * Shows any **transients that were set**, along with their timeout, component, and call stack
 * Shows all **WordPress conditionals** on the current page, highlighted nicely
 * Shows an overview including page generation time and memory limit as absolute values and as % of their respective limits
 * Shows all *scripts and styles* which were enqueued on the current page, along with their URL, dependencies, dependents, and version number

= Authentication =

By default, Query Monitor's output is only shown to Administrators on single-site installs, and Super Admins on Multisite installs.

In addition to this, you can set an authentication cookie which allows you to view Query Monitor output when you're not logged in (or if you're logged in as a non-administrator). See the bottom of Query Monitor's output for details.

== Installation ==

You can install this plugin directly from your WordPress dashboard:

1. Go to the *Plugins* menu and click *Add New*.
2. Search for *Query Monitor*.
3. Click *Install Now* next to the Query Monitor plugin.
4. Activate the plugin.

Alternatively, see the guide to [Manually Installing Plugins](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Screenshots ==

1. The admin toolbar menu showing an overview
2. Aggregate database queries by component
3. Slow database queries highlighted in a separate panel
4. Database queries complete with filter controls
5. Hooks and actions
6. HTTP requests (showing an HTTP error)
7. Aggregate database queries grouped by calling function

== Frequently Asked Questions ==

= Who can see Query Monitor's output? =

By default, Query Monitor's output is only shown to Administrators on single-site installs, and Super Admins on Multisite installs.

In addition to this, you can set an authentication cookie which allows you to view Query Monitor output when you're not logged in (or if you're logged in as a non-administrator). See the bottom of Query Monitor's output for details.

= Does Query Monitor itself impact the page generation time or memory usage? =

Short answer: Yes, but only a little.

Long answer: Query Monitor has a small impact on page generation time because it hooks into a few places in WordPress in the same way that other plugins do. The impact is negligible.

On pages that have an especially high number of database queries (in the hundreds), Query Monitor currently uses more memory than I would like it to. This is due to the amount of data that is captured in the stack trace for each query. I have been and will be working to continually reduce this.

= Are there any add-on plugins for Query Monitor? =

Query Monitor transparently supports add-ons for the Debug Bar plugin. If you have any Debug Bar add-ons installed, just deactivate Debug Bar and the add-ons will show up in Query Monitor's menu.

There's also [Query Monitor bbPress & BuddyPress Conditionals](https://wordpress.org/plugins/query-monitor-bbpress-buddypress-conditionals/) by Stephen Edgar.

= Where can I suggest a new feature or report a bug? =

Please use [the issue tracker on Query Monitor's GitHub repo](https://github.com/johnbillion/query-monitor/issues) as it's easier to keep track of issues there, rather than on the wordpress.org support forums.

= Do you accept donations? =

No, I do not accept donations. If you like the plugin, I'd love for you to [leave a review](https://wordpress.org/support/view/plugin-reviews/query-monitor). Tell all your friends about the plugin too!

== Changelog ==

= 2.7.4 =
* An unknown component now gets marked as such, not as Core.
* Support for invokable objects in action and filter callbacks.
* Fix fatal error when activating Debug Bar plugin after Query Monitor has already been activated.
* Implement escaping inside `QM_Output_Html::format_url()` which can deal with unsafe output. Thanks to Stephen Harris for the responsible disclosure.

= 2.7.3 =
* Improvements to the shutdown handler for PHP errors, so it handles syntax and compilation errors too.

= 2.7.2 =
* Implement a shutdown handler for PHP errors to avoid fatals being unintentionally hidden when `display_errors` is on.
* Don't attempt to do anything with scripts and styles if a corresponding header action hasn't fired.
* Don't sort the enqueued scripts and styles, so they're output in the order in which they're enqueued.
* For the time being, let's not load QM when using the CLI because we've no persistent storage and no means of outputting collected data on the CLI.
* Call static methods using their class name, not a variable. Fixes compatibility with PHP 5.2.

= 2.7.1 =
* Display a warning (rather than triggering a fatal error) for scripts and style dependencies which have gone missing during the duration of the page load.
* Tweak some more Debug Bar add-on styles.
* Ensure erroneous non-SELECT queries are also highlighted in red.
* Further tweaks to QM's output if JavaScript isn't available for any reason.
* Add PHP4-style constructors to the Debug Bar classes to avoid fatals with Debug Bar add-ons which are explicitly using them.
* In the event that QM's JavaScript doesn't get enqueued, force the QM output to display so users can at least debug the issue.
* Remove the abstract `output()` methods from abstract classes which implement `QM_Output` to avoid PHP bug #43200.
* Fixing a notice in the admin component when `get_current_screen()` isn't an object.

= 2.7.0 =
* Detect broken dependencies for scripts and styles.
* Calculate and output the dependents of scripts and styles.
* Add transparent support for Debug Bar add-on panels.
* Add support for WordPress.com VIP plugins in the component detection.
* Sortable and filterable columns for HTTP requests.
* Display a warning when something's hooked onto the `all` action.
* Clearer output for the template file and component names when a child theme is in use.
* Move the current blog information to the Request component. Display current site information if we're running a multi-network.
* Allow default error handlers, such as error logging, to continue to function as expected.
* Don't skip outputting the calling function name in the database error list.
* New namespaced filter names for a bunch of filterable things.
* Add a `qm/process` filter to allow users to disable QM's processing and output.
* Display the value of `WP_HTTP_BLOCK_EXTERNAL` and `WP_ACCESSIBLE_HOSTS` in the HTTP component.
* New storage and registration mechanisms for collectors, dispatchers, and output handlers.
* CSS tweaks to better match wp-admin styles.

= 2.6.10 =
* Add compatibility with PHP <5.3.6. `DirectoryIterator::getExtension()` isn't available on this version (and also as it's part of SPL it can be disabled).
* Simplify the admin CSS to avoid QM's output being covered by the admin menu.
* Add support for footer styles in the scripts and styles component.
* Update the authentication JavaScript so it works cross-protocol.
* Add support for footer styles in the scripts and styles component.

= 2.6.9 =
* New Scripts & Styles component
* Support for the new `is_customize_preview()` conditional
* More robust handling of HTTP requests short-circuited with `pre_http_request`
* Introduce a `query_monitor_silent_http_error_codes` filter to allow certain `WP_Error` codes to be silenced in HTTP requests
* Split SQL queries on LEFT, OUTER, and RIGHT too
* Gracefully avoid fatal errors if a site is moved and the db.php symlink is no longer pointing to the correct location
* Pause Infinite Scroll when Query Monitor is viewed
* Support the new admin menu behaviour in WP 4.1
* Fix the positioning of output when using the Twenty Fifteen theme
* Switch to an AJAX call for setting and clearing QM's authentication cookie

= 2.6.8 =
* RTL layout tweaks
* Correct the component detection logic so it's more accurate
* Re-implement output on the login screen which went missing
* Display a few more proxy and debugging related constants

= 2.6.7 =
* Use an actual authentication cookie instead of a nonce in the Authentication component
* Implement some extra methods of determining the current user/group
* Move the loading of dispatchers to the `plugins_loaded` hook so plugins can add their own
* Misc performance improvements

= 2.6.6 =
* More robust support for alternative database drivers (including `mysqli` in core)
* Avoid warnings and notices when a custom database class is in place and it's not saving queries (ie. HyperDB)
* Better handling when certain functions (such as `memory_get_peak_usage()`) are disabled

= 2.6.5 =
* Avoid the "Class 'QM_Backtrace' not found" error
* Correct the layout of the Slow Queries and Query Errors panels
* Move back-compat CSS into its own file
* Huge simplification of code in `db.php` by using `parent::query()`
* Misc visual tweaks

= 2.6.4 =
* Introduce sortable columns for database query times and numbers
* Display the queried object in the Request panel
* Fix the admin menu behaviour when viewing QM output
* Fixes for output buffering and AJAX requests
* Several bits of code cleanup

= 2.6.3 =
* Clickable stack traces and file names if you've configured Xdebug's `file_link_format` setting
* Show the number of times each PHP error has been triggered
* Visual bugfixes when using Firefox
* Fix a bug which was preventing AJAX debugging from being output
* Fix a fatal error when using PHP 5.2 on Windows
* Display HTTP proxy information when appropriate
* Introduce the `QM_DISABLE` constant for unconditionally disabling Query Monitor
* Always return true from our PHP error handler to suppress unwanted PHP error output (eg. from Xdebug)
* Internals: Much more robust logic and even more separation of data collection and output
* Internals: Many performance improvements

= 2.6.2 =
* Fix two fundamental stability and compatibility issues (great news)
* Various visual tweaks
* Handle some uncommon use cases of the HTTP API

= 2.6.1 =
* Remove a file that was accidentally committed to the wordpress.org repo

= 2.6 =
* Toggleable stack traces for queries
* Show deprecated errors in the PHP Errors panel
* Replace the Query Vars panel with a Request panel with more information
* Display a warning when `db.php` isn't in place
* Fix some PHP 5.2 compatibility
* Considerable restructuring of the underlying code to increase abstraction

= 2.5.6 =
* Fix the "Invalid header" issue. Woo!

= 2.5.5 =
* Better layout for the Hooks panel
* Fix some AJAX issues
* Fix some output buffer compatibility issues which were causing fatal errors

= 2.5.4 =
* Avoid a fatal error when strict errors are triggered at compile time
* Avoid a warning when PDO or Mysqli is in use
* Updated CSS for WordPress 3.8. Retains support for default 3.7 and MP6 on 3.7
* Tweak PHP error_reporting in the Environment component

= 2.5.3 =
* Show an inline error when a hook has an invalid action
* Show a warning in the admin toolbar when HTTP requests fail
* Fix the time shown when filtering queries
* Fix empty stack traces (regression at some point)

= 2.5.2 =
* Prevent uncaught exceptions with static method actions
* Misc formatting tweaks

= 2.5.1 =
* Un-break query filtering
* Performance improvements

= 2.5 =
* Display the component for HTTP requests, transients, PHP errors, and hook actions
* Improved visual appearance and layout
* Add an action component filter to the Hooks panel
* Log errors returned in the `pre_http_request` filter
* `QM_DB_LIMIT` is now a soft limit
* Performance improvements

= 2.4.2 =
* Add a hook name filter to the Hooks panel
* Update db.php to match latest wp-db.php
* Avoid fatal error if the plugin is manually deleted
* Add the new `is_main_network()` conditional
* Lots more tweaks

= 2.4.1 =
* Un-break all the things

= 2.4 =
* New Redirect component
* Add support for strict errors
* Display the call stack for HTTP requests
* Display the call stack for transients
* Remove pre-3.0 back-compat code
* Many other bugfixes and tweaks

= 2.3.1 =
* Compat with Xdebug
* Display the call stack for PHP errors

= 2.3 =
* Introduce AJAX debugging (just PHP errors for now)
* Visual refresh
* Add theme and stylesheet into to the Theme panel

= 2.2.8 =
* Add error reporting to the Environment panel

= 2.2.7 =
* Don't output QM in the theme customizer

= 2.2.6 =
* Add the database query time to the admin toolbar
* Various trace and JavaScript errors

= 2.2.5 =
* Load QM before other plugins
* Show QM output on the log in screen

= 2.2.4 =
* Add filtering to the qyer panel

= 2.2.3 =
* Show component information indicating whether a plugin, theme or core was responsible for each database query
* New Query Component panel showing components ordered by total query time

= 2.2.2 =
* Show memory usage as a percentage of the memory limit
* Show page generation time as percentage of the limit, if it's high
* Show a few bits of server information in the Environment panel
* Log PHP settings as early as possible and highlight when the values have been altered at runtime

= 2.2.1 =
* A few formatting and layout tweaks

= 2.2 =
* Breakdown queries by type in the Overview and Query Functions panels
* Show the HTTP transport order of preference in the HTTP panel
* Highlight database errors and slow database queries in their own panels
* Add a few PHP enviroment variables to the Environment panel (more to come)

= 2.1.8 =
* Change i18n text domain
* Hide Authentication panel for non-JS
* Show database info in Overview panel

= 2.1.7 =
* Full WordPress 3.4 compatibility

= 2.1.6 =
* Small tweaks to conditionals and HTTP components
* Allow filtering of ignore_class, ignore_func and show_arg on QM and QM DB

= 2.1.5 =
* Tweak a few conditional outputs
* Full support for all WPDB instances
* Tweak query var output
* Initial code for data logging before redirects (incomplete)

= 2.1.4 =
* Add full support for multiple DB instances to the Environment component
* Improve PHP error function stack

= 2.1.3 =
* Fix display of wp_admin_bar instantiated queries
* Fix function trace for HTTP calls and transients

= 2.1.2 =
* Lots more behind the scenes improvements
* Better future-proof CSS
* Complete separation of data/presentation in db_queries
* Complete support for multiple database connections

= 2.1.1 =
* Lots of behind the scenes improvements
* More separation of data from presentation
* Fewer cross-component dependencies
* Nicer way of doing menu items, classes & title

= 2.1 =
* Let's split everything up into components. Lots of optimisations to come.

= 2.0.3 =
* Localisation improvements

= 2.0.2 =
* Admin bar tweaks for WordPress 3.3
* Add some missing l10n
* Prevent some PHP notices

= 2.0.1 =
* Just a few rearrangements

= 2.0 =
* Show warnings next to MySQL variables with sub-optimal values

= 1.9.3 =
* Fix list of non-default query vars
* Fix list of admin screen column names in 3.3
* Lots of other misc tweaks
* Add RTL support

= 1.9.2 =
* Lots of interface improvements
* Show counts for transients, HTTP requests and custom query vars in the admin menu
* Add backtrace to PHP error output
* Hide repeated identical PHP errors
* Filter out calls to _deprecated_*() and trigger_error() in backtraces
* Show do_action_ref_array() and apply_filters_ref_array() parameter in backtraces
* Remove the 'component' code
* Remove the object cache output
* Add a 'qm_template' filter so themes that do crazy things can report the correct template file

= 1.9.1 =
* Display all custom column filter names on admin screens that contain columns

= 1.9 =
* Display more accurate $current_screen values
* Display a warning message about bug with $typenow and $current_screen values
* Improve PHP error backtrace

= 1.8 =
* Introduce a 'view_query_monitor' capability for finer grained permissions control

= 1.7.11 =
* List body classes with the template output
* Display calling function in PHP warnings and notices
* Fix admin bar CSS when displaying notices
* Remove pointless non-existant filter code

= 1.7.10.1 =
* Fix a formatting error in the transient table

= 1.7.10 =
* Tweaks to counts, HTTP output and transient output
* Upgrade routine which adds a symlink to db.php in wp-content/db.php

= 1.7.9 =
* PHP warning and notice handling
* Add some new template conditionals
* Tweaks to counts, HTTP output and transient output
