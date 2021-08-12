=== Enable jQuery Migrate Helper ===
Contributors: wordpressdotorg, clorith, azaozz
Tags: jquery, javascript, update
Requires at least: 5.4
Tested up to: 5.6
Stable tag: 1.3.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get information about calls to deprecated jQuery features in plugins or themes.

== Description ==

With the update to WordPress 5.5, a migration tool known as `jquery-migrate` was no longer enabled by default. This may lead to lacking functionality or unexpected behavior in some themes or plugins that run older code.

This plugin serves as a temporary solution, enabling the migration script for your site to give your plugin and theme authors some more time to update, and test, their code.

---

With the update to WordPress 5.6, the included version of jQuery is also upgraded. This means that old code that previously caused warnings now may instead may cause errors or stop working entirely.

Some of the features no longer working will just stop working behind the scenes without any apparent problem.

The plugin will let you downgrade to a previous version of jQuery for a period, but as a site administrator you are encouraged to get the underlying issue fixed.

== Frequently Asked Questions ==

= What does it mean that something is “deprecated” =
A script, a file, or some other piece of code is deprecated when its developers are in the process of replacing it with more modern code or removing it entirely.

= What happens after WordPress 5.6 =
With the release of WordPress 5.6, the jQuery version also gets updated. This means that plugins or themes that previously caused deprecation warnings now instead will cause errors.
This plugin will allow you to, temporarily, return to the previous version of jQuery if this happens (it will also try to do so automatically for website visitors the first time an error happens) allowing you to fix the code, or replace it.

= How do I find and use the browser console =
WordPress.org has an article about [using the browsers console log to diagnose JavaScript errors](https://wordpress.org/support/article/using-your-browser-to-diagnose-javascript-errors/).

= The plugin isn't logging deprecations or changing jQuery versions =
If your site has any plugins for combining JavaScript files, or loading them asynchronously, this plugin may be negatively affected and not be able to operate as intended.

If your site requires this plugin to operate, please disable any plugins which interact with the loading of JavaScript files such as the types mentioned above. Once the underlying issue has been resolved, you may remove this plugin and re-enable those other tools.

= How do I know if I need this plugin, or not=
If something isn’t working correctly on your site after you upgraded WordPress, then you can simply try installing and activating this plugin. If this helps, then you leave this plugin activated and follow the instructions in the plugin. The plugin will tell you when you don’t need it any more.

= There are a lot of deprecation warnings when using jQuery version 3 =
As jQuery version 3 is very new to WordPress, this is expected.

Deprecated notices means that the Migration tool is in place making sure these features still continue working while the related code is updated.

== Installation ==

1. Upload to your plugins folder, usually `wp-content/plugins/`.
2. Activate the plugin on the plugin screen.
3. That's it! The plugin handles the rest automatically for you.

== Changelog ==

= v 1.3.0 =
* Added legacy jQuery UI to be loaded if legacy jQuery is in use.
* Added mention of site URLs in automatic emails.
* Added option to enable/disable automatic downgrades.
* Added logic to ensure only one downgrade request is sent per page load.
* Updated logic around automatic downgrades for improved performance.
* Fixed core deprecation notices being incorrectly labeled as undetermined inline ones.

= v 1.2.0 =
* Added settings page
* Added option for downgrading to legacy jQuery
* Added automatic downgrades
* Added option to log deprecations in modern jQuery
* Added e-mail notifications
* Added weekly email digest of deprecations
* Added option to allow logging deprecations from anonymous site visitors
* Changed the  handling of inline JavaScript code causing deprecation notices
* Changed the admin bar to be two fixed links to avoid ever changing contexts
* Changed the admin notices to be persistent when using legacy jQuery after upgrading to WordPress 5.6
* Changed how concatenation is disabled, to address public-facing performance concerns
* Fixed recommendation to remove plugin when not logging any deprecations having the wrong logic and not being displayed.

= v 1.1.0 =
* Added option to dismiss deprecation notices in backend
* Added logging of deprecation notices in the front end
* Added admin bar entry to show when deprecations occur
* Added view of logged deprecations
* Added dashboard notice encouraging users to remove the plugin if no deprecations have been logged in a while (1 week).
* Changed the time interval between showing the dashboard nag from 2 weeks to 1 week, as WordPress 5.6 comes closer.

= v 1.0.1 =
* Fix one of the admin notices being non-dismissible.

= v 1.0.0 =
* Initial release.
