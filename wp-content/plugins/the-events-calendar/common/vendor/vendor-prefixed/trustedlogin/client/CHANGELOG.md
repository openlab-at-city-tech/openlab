# Changelog for TrustedLogin Client

## 1.9.0 (August 25, 2024)

- Added a minimum `vendor/namespace` length of five characters to help prevent collisions with other instances
- Fixed a flash of un-styled content on the Grant Access screens by outputting CSS earlier
- Addressed potential error when the `WP_Filesystem` class is not found
- Moved TrustedLogin images to inline CSS to simplify the build process
  - Removed need for `--relative_images_dir` flag in `build-sass` script
  - Removed `src/assets/loading.svg`
  - Removed `src/assets/lock.svg`
- Improved coding standards and documentation

## 1.8.0 (July 18, 2024)

- Implemented many speed enhancements
- Moved logging directory creation into own private method: `Logging::setup_logging_directory()` to clean up the `Logging::setup_klogger()` method
- Now compliant with WordPress PHPCS
- Use `gmdate()` instead of `date()` for log files and for users registration dates
- Moved `SecurityChecks::get_ip()` to `Utils::get_ip()`
- Added `Utils::get_user_agent()` to generate a user agent string with an optional max length
- Improved handling of potential errors
- Security enhancements
  - Escaped all error messages
  - Removed usage of `$_REQUEST` in favor of `$_POST` and `$_GET`
- Implemented PHPCS and PHPStan checks (thanks, [Daniel](https://code-atlantic.com))

## 1.7.0 (January 29, 2024)

- Added Utils class to handle common utility functions
- Converted usage of `get_site_transient()` and `set_site_transient()` to using `Utils::get_transient()` and `Utils::set_transient()`.
  - Scopes the storage to each blog instead of per-network, preventing potential issues with multisite
  - Fixes potential issues with object caching plugins that don't support transients, while allowing for auto-expiring data to be stored in the database
  - Prevents data from being "cleaned up" by site optimization plugins that remove expired transients

## 1.6.2 (January 26, 2024)

- Removed unnecessary request body when revoking site access
- Added index.php files to prevent directory listings
- Added check for a potential error when revoking support user

## 1.6.1 (September 22, 2023)

- Removed unnecessary payload when revoking site access
- Improved error logging:
  - Added error data to the logging, in addition to the code & message
  - Now returns the full API response when the response body is invalid
  - Switched to just-in-time creation of logging directory and log file
  - Added "Learn more" link to the logging directory `index.html`
  - Renamed the log files to `client-{namespace}-{Y-m-d}-{hash}.log` to be easier to distinguish and less verbose
- Fixed AJAX status code not being properly set when encountering an error

## 1.6.0 (September 7, 2023)

- Added `clone_role` configuration setting to allow the support user to be created with an existing role, rather than a clone of a role
- Added a `trustedlogin_{ns}_support_role` capability to the cloned support user role in order to better identify that the role is created by TrustedLogin
- Added `terms_of_service/url` setting to allow linking to a custom terms of service page
  - If not defined, the Terms of Service text and link will not be shown
- Converted CSS generation to use SCSS mixins to allow easier overrides by themes and plugins
- Removed borders around the role descriptions in the Grant Access form
- Clarified the language surrounding user roles in the Grant Access form
- Moved the admin toolbar link to next to the "Howdy, {username}" menu
  - Relabeled the link from "Revoke TrustedLogin" to "Revoke Access"
- Improved user creation flow to prevent errors when creating a user with an existing email address
- Fixed error when using PHP in strict mode
- Fixed error creating the support user when the `vendor/website` configuration exceeded 100 characters in length

## 1.5.1 (2023-04-18)

- Fixed PHP error caused by HEREDOC template formatting in `Form.php`

## 1.5.0 (2023-04-13)

- Added the ability for users to create support tickets when granting access—to enable, set `webhook/create_ticket` to `true` in the configuration array
  - Added second parameter, `$ticket_data` to `Client::grant_access()` method
  - Added `ticket` to the webhook data, with the following keys:
	- `message` (string)
- Added `Config::get_settings()` public method to get all settings
- Added `Encryption::get_remote_encryption_key_url()` public method to get the final URL used to fetch the vendor public key
- Added `Logging::get_log_file_path()` public method to get the full path to the log file
- Filtered the `$_POST` request that generates access to allow only defined fields
- Created new `Form.php` file and `Form` class to handle form rendering
  - Moved form-related methods from `Admin` to `Form`
- Modified `auth.scss` to support new ticket fields, admin debugging rendering, and improve styling

## 1.4.0 (2023-03-01)

- Added ability to send debug data, generated using the WordPress Site Health report, via webhook
- Added `Client::get_debug_data()` private method
- Modified the `webhook_url` configuration setting to be an array. Now, `webhook` is an array of `url` and `debug_data` keys
  - Passing `webhook_url` is still supported for backwards compatibility
- The SDK will no longer load on sites that lack Sodium, which is bundled with PHP 7.2+ and WordPress 5.2+, and available as a [PECL extension](https://pecl.php.net/package/libsodium) for PHP 7.0 and 7.1
- Added a public `Encryption::meets_requirements()` method to check whether the site meets the requirements for encryption
- Removed all Composer package dependencies
  - Added our own logging class
- Fixed typo in `trustedlogin/{namespace}/license_key` filter name

## 1.3.7 (2022-11-08)

- Improved styling of the authorization form
- Fixed `php-scoper` support by setting the root namespace for `\WP_Error` and `\WP_User`
- Fixed the role message always showing "similar" to a role when it was the same role
- Fix docblock to prevent Strauss from namespacing it

## 1.3.6 (2022-10-13)

- Fixed hard-coded message about the support user being created "1 day ago"
- Added missing translation hints
- Updated npm dependencies

## 1.3.5 (2022-10-12)

- Fixed rescheduling cron hooks when support access is extended

## 1.3.4 (2022-10-11)

- Changed to use `hash()` instead of `wp_hash()` for log naming; `wp_hash()` can be overridden, which is potentially insecure
- Switched to naming logs using a `sha256` hash for additional security

## 1.3.3 (2022-10-02)

- Fixed logging an error when license key configuration was undefined

## 1.3.2 (2022-09-30)

- Added `trustedlogin/{ns}/vendor/public_key/website` filter to modify the website used to fetch public key (this can be helpful when running tests)
- Added `.tl-client-grant-button` and `.tl-client-revoke-button` CSS classes to the respective buttons in the Auth screen
- Changed logging levels from `notice` to `error` when fetching the Vendor public key fails

## 1.3.1 (2022-09-21)

- Fixed PHP 8.1 warning related to performing string actions on `null`

## 1.3 (2022-08-12)

- Changed Now display the reference ID by default in both the login screen and the Grant Access screen
- Added `trustedlogin/{ns}/template/auth/display_reference` filter to control whether the reference ID is shown in the access form
- Added error handling when `SiteAccess::get_access_key()` fails

## 1.2 (2022-01-25)

- Fixed WordPress Multisite support  ([#84](https://github.com/trustedlogin/client/issues/84))
  - Also run `wpmu_delete_user()` when deleting support users
  - Use `{get|update|delete}_site_option` instead of `{get|update|delete}_option`
  - Add blog ID to hashes for unique keys and email hashes for each blog on a network
- Fixed hashing an empty string when no license was supplied
- Removed unnecessary database call by only registering endpoint when there's a valid TrustedLogin login request ([#75](https://github.com/trustedlogin/client/issues/75))
- Revoke TrustedLogin now always points to the Dashboard
  - Removed second argument from the `SupportUser::get_revoke_url()` method (`$current_url`)
- Added namespace in passed webhook data (under the key `ns`) to allow for more complex webhook functionality ([#83](https://github.com/trustedlogin/client/issues/83))

## 1.1 (2021-12-13)

- Improved admin menu configuration
  - Enhanced logic around whether and how to add a TrustedLogin menu to the sidebar depending on the `menu/slug` setting:
    - If `null`, the a top-level menu will be added.
    - If `false`, a menu item will not be added.
    - If a string, the `menu/slug` setting will be used as the `$parent_slug` argument passed to the [`add_submenu_page()` function](https://developer.wordpress.org/reference/functions/add_submenu_page/)
  - Added `menu/icon_url` setting that is used as the `$icon_url` parameter in [`add_menu_page()` function](https://developer.wordpress.org/reference/functions/add_menu_page/)
- If granting access fails, the reference ID is now passed onto the support URL using the `?ref=` query parameter
- Removed third arguments for `trustedlogin/{ns}/template/auth` and `'trustedlogin/{ns}/template/auth/footer_links` filters, since the namespace is already known
- Improved WordPress backward-compatibility by removing usage of:
  - `wp_date()` (added in WordPress 5.3); used `DateTime` instead
  - `wp_clear_scheduled_hook()` (added in WP 4.9); used `wp_clear_scheduled_hook()` instead
- Fixed filter naming: `trustedlogin/{ns}/public_key` renamed to `trustedlogin/{ns}/vendor_public_key`

## 1.0.2 (2021-10-07)

- Added `SupportUser::is_active()` method to check whether the passed user exists and has an expiration time in the future
- Added `ref` to the to `trustedlogin/{namespace}/access/extended` hook `$data` argument
- Modified some `WP_Error` error codes to be more consistent

## 1.0.1 (2021-09-27)

- Fixed issue where non-support users may see the "Revoke TrustedLogin" admin bar link

## 1.0.0 (2021-09-22)

This is the initial production release of TrustedLogin! Thank you to everyone who has worked on the project, including [Hector Kolonas](https://github.com/inztinkt), [Josh Pollock](https://github.com/Shelob9), and [Shawn Hooper](https://github.com/shawnhooper).

In addition, a deep thanks to our security auditors: James Golovich with [Pritech](https://www.pritect.net) and Ryan Dewhurst with [WPScan](https://wpscan.com).
