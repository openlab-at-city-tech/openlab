
=== Broken Link Checker ===
Contributors: managewp, wpmudev
Tags: links, broken links, internal link, external link, broken images, seo, test links, check links, bad links
Requires at least: 5.2
Tested up to: 6.4
Stable tag: 2.2.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Check posts, pages & all content for broken links & missing images to improve SEO. Get notified when bad links are found.

== Description ==
The fastest and most accurate broken link checker for WordPress. Join over 700K people who use Broken Link Checker to monitor and detect broken links 20x faster than other plugins, with pinpoint accuracy. No page limits, no ads, no upsells. Enjoy effortless, unlimited, and automated broken link checking from the get-go.

**Broken links can kill your SEO, user experience, and site health… Broken Link Checker helps you find and resolve them faster than similar plugins.**

Finding and staying on top of broken links can feel like a never-ending battle, especially if you're dealing with large sites or managing multiple.

Using a link checking tool can save you from having to find links manually, or avoid the frustration of discovering missed broken links months later (oops!).

But not every broken link checker was created equal…

You need a tool that not only automates the process, but also detects broken links quickly, thoroughly, and accurately.

**That’s where Broken Link Checker comes in…**

BLC doesn’t *just* scan your sites faster than other solutions, it scans deeply and frequently to ensure no broken links are missed.

Scan every inch of your sites, including external and internal links within your posts, pages, comments, blogroll, even custom fields.

It’ll also detect missing images and redirects, helping to ensure your site's SEO and user experience are not compromised in any way. And if that isn't enough, you’ll also receive detection reports via email so you can quickly take corrective action.

**Choose from two different link checking engines:**

Broken Link Checker gives you the option of using two unique engines based on your needs. You can also easily switch between local and cloud broken link checking **without losing your saved settings.**

**1.Cloud-based link checker**

This engine is 100% cloud-based and built directly on WPMU DEV’s servers (the owners of BLC), harnessing their powerful API.

*Note that our cloud-based engine is still in beta. We’re working around the clock to make this new version even better. As mentioned, if it’s missing a feature you need, simply switch back to the local version.*

This cloud-based engine gives you several advantages, including:

-   **20x more speed** - Being cloud-based eliminates any dependence on the performance of your server, giving you a massive speed increase.

-   **Unlimited page & URL checker** - Enjoy limitless scanning for pages and URLs, ideal if you’re working with multiple sites or large sites with a lot of content.
-   **Edit/unlink broken links (coming soon)** - Effortlessly resolve all detected links right from your BLC dashboard.

-   **Crawl it all** - Standard pages and posts, menus, category pages... BLC ensures no URL is missed.

-   **Any site, any size** - Unlike plugin-based solutions, BLC is server-independent, making it capable of checking sites of any size and type.

- **Fewer errors** - BLC’s cloud-based nature eliminates the occurrence of WP Errors, Fatal Errors, DB Errors, and PHP Errors often present with other plugins.

- **Less chance of being blocked** - The cloud-based engine doesn't use your site's IP address to check external URLs, meaning you’re less likely to be blocked due to frequent requests.

**Plus, manage broken links for unlimited sites from one place**

Since the cloud-based engine connects you to WPMU DEV, you have the luxury of free access to their suite of powerful web development tools - including a convenient site management interface where you can manage broken links on multiple sites, rather than just one.

**2. Local link checker**

The main difference of our local link checker compared to the cloud engine, is that it uses your site/server resources to fetch and analyze site content and check the status of links.

It also uses your site's IP address, which as mentioned above, increases your risk of being blocked by some external links, resulting in incomplete or inaccurate scan results.

**Here are the standout features of the local link checking engine:**

-   Monitors external links and internal links in your posts, pages, comments, the blogroll, and custom fields (optional).
-   Detects broken links, missing images, and redirects.
-   Receive notifications via your dashboard or email.
-   Make broken links display differently in posts (optional).
-   Prevent search engines from following broken links (optional).
-   Search and filter links by URL, anchor text and more.
-   Edit links directly from your interface, without manually updating each post.

Whether you choose local or cloud-based, BLC’s user-friendly dashboard and powerful features makes staying on top of broken links as easy and automated as possible.

### The best scanning & detection features on one dashboard:

**Run manual scans or set an automatic schedule**

Enjoy the flexibility to run scans manually, or schedule automatic daily, weekly, and monthly scans to suit your preference and the sites you manage.

**Edit or unlink broken links (coming soon to cloud-based)**

After BLC detects broken links, it gives you the option to edit or unlink them with a click from your main dashboard. It couldn’t be easier or faster.

**Detailed broken link reports**

After each scan you get a comprehensive summary of any broken links detected, along with the status of the links so you can instantly know how to resolve the issue.

**Detection notifications via email**

Stay on top of your link health and fix issues as fast as possible with broken link reports sent straight to an email address of your choice.

**Advanced search filtering**

BLC’s search and filtering tools make it faster and easier to find specific URLs or filter by status or error type.

**Export report lists for further analysis**

Export your broken link lists as a CSV file for greater analysis inside of Google Sheets, Microsoft Excel, and other spreadsheet/data software.

### ABOUT US

The developers of BLC, WPMU DEV is an all-in-one WordPress platform that has helped millions of web developers worldwide run and grow their businesses for over 15 years.

Our WordPress offerings include site management tools, dedicated hosting, reseller domains, and 12 popular WP plugins totalling more than 80 million downloads. Check out our website: [https://wpmudev.com/](https://wpmudev.com/)

Oh, and one more thing… we hope you enjoy our free offerings as much as we’ve loved making them for you!

== Changelog ==

= 2.2.3 =
* Enhance: Optimize local db tables.
* Fix: Rare but possible Fatal error when unlinking a link.
* Fix: Removed deprecated Defender hooks.

= 2.2.2 =
* Fix: Exclusions list spacing in Local settings.
* Fix: Database error Illegal mix of collations.
* Enhance: Use WP_HTML_Tag_Processor and DOMDocument instead of regex in Cloud Edit/Unlink.

= 2.2.1 =
* Fix: Youtube playlist show as not found (Local engine).
* Fix: In Scheduled scan, users can be added twice in list.
* Fix: PHP8 Deprecation messages.
* Fix: Callback missing from Local cron jobs.
* Enhance: Added BLC bot UA in Defender's bot allowlist.

= 2.2.0 =
* New: Edit and Unlink broken links in Cloud Engine.
* Fix: Editor access blocked in Local Engine.
* Enhance: PHP 8.1 and 8.2 compatibility.

= 2.1.0 =
* Improved admin menus, so they are separated to Cloud and Local Engine.
* Fix multiple emails issue.
* Fix Dashboard fonts.
* Fix insisting line-through text decoration on links in front end.

= 2.0.0 =
* Version 2 release
* Added connection to free Hub features for cloud based Broken Link Checker.

= 1.11.21 =
* Fix strict comparison causing db queries

= 1.11.20 =
* Fix XSS vulnerability

= 1.11.19 =
* Enhance compatibility with WordPress 6.1
* Fix deprecated sanitization filter FILTER_SANITIZE_STRING

= 1.11.18 =
* Updated UA string

= 1.11.16 =
* Enhance compatibility with PHP 8
* Enhance compatibility with WordPress 5.8
* Remove donation links and adds

= 1.11.15 =
* Added integration with WP External Links plugin

= 1.11.14 =
* Added compatibility for WP 5.5.

= 1.11.13 =
* Added filter for disabling email notifications.
* Added filter to use GET requests. (Thanks to [Stian Lund](https://github.com/Pathduck))
* Added option to disable last updated time on post types.
* Added option to check post excerpts.
* Added a confirmation box when unlinking.
* Added basic multisite support.
* Added proper error messages on YouTube video errors.
* Fixed bulk recheck option.
* Fixed minor database errors.
* Improved performance.
* Removed default YouTube API key.
* Removed usage of depricated WP Snoopy.

= 1.11.12 =
* Added an interface to use personal YouTube API Key
* Fixed database errors on installation for some hosts.
* Fixed link editor not working with elementor.
* Fixed custom field key with multiple keys not rendering.
* Fixed compatibility issues with Business Directory plugin.
* Fixed bulk actions not working properly for some actions.
* Updated the owner on repository.

= 1.11.11 =
* Fixed SQL errors caused on some hosts.
* Fixed search filters & pagination.
* Fixed link table design in mobile devices.
* Fixed deprecated functions on PHP 7.4
* Fixed filter_var deprecated notices.
* Improved code with WPCS.
* Added a new API key for the YouTube API.

= 1.11.10 =
* Fixed create_function deprecated notices.
* Fixed missing license information.
* Fixed table prefix on delete post action (Thanks to [Guillaume Boudreau](https://github.com/gboudreau))
* Merges cron schedules into 1 hook (Thanks to [Der Mundschenk & Compagnie](https://github.com/mundschenk-at))
* Add escaping to search params.
* Tested up to WordPress 5.3
* Updated laguage files

= 1.11.9 =
* Fixed Reflected XSS (Thanks to [Tobias Fink](https://github.com/sbaresearch/advisories/tree/public/2019/SBA-ADV-20190913-02_WordPress_Plugin_Broken_Link_Checker) for the report)
* Fixed param escaping to avoid possible XSS attack.

= 1.11.8 =
* Various fixes.

= 1.11.7 =
* Fixed a PHP 7.2 compatibility issue

= 1.11.6 =
* Fixed link check not saving

= 1.11.5 =
* Fixed a PHP 5.2 compatibility issue

= 1.11.4 =
* Fixed a few more PHP 7.x/5.6 compatibility issues

= 1.11.3 =
* Updated plugin information

= 1.11.2 =
* Fixed a compatibility issue

= 1.11.1 =
* Major performance improvement. Database queries reduced up to 10x in some cases.
* Feel free to contribute to the plugin on [GitHub](https://github.com/wpmudev/broken-link-checker). Pull requests welcome!

= 1.10.11 =
* Fixed the issue with HTTPS (Thanks to [gmcinnes](https://wordpress.org/support/profile/gmcinnes))
* Broken Link Checker is now on [GitHub](https://github.com/wpmudev/broken-link-checker). Pull Requests welcome.

= 1.10.10 =
* New plugin image that was long overdue.

= 1.10.9 =
* Fixed a security vulnerability where part of the log data visibile in the "Details" view was not properly sanitized.
* Updated French translation.
* Updated Portuguese translation.
* Removed an advertising banner.

= 1.10.8 =
* Added a Swedish translation.
* Fixed an encoding-related bug that caused some translated text on the "Broken Links" page to show up as gibberish.
* Fixed a potential security vulnerability where the "Final URL" field was not sanitized.
* Fixed link text being truncated to 250 characters.
* Fixed the "Edit URL" function updating the link text even when the user left that field unchanged.
* Tested up to 4.2.1.

= 1.10.7 =
* Tested up to WordPress 4.2.

= 1.10.6 =
* Fixed a serious CSRF/XSS vulnerability.
* Switched to YouTube API v3. The old API version will be shut down on April 20, so the plugin needs to be updated to continue checking links to YouTube videos.
* Fixed long URLs overflowing into adjacent table columns.
* Fixed a few minor PHP strict-mode notices.
* Added database character set to the "Show debug info" table.

= 1.10.5 =
* Security: Fixed an XSS vulnerability that could be used by Editors and Administrators to inject arbitrary HTML/JS code in the "Tools -> Broken Links" page.
* Other minor security fixes.
* Tested on WordPress 4.2 beta.

= 1.10.4 =
* Tested on WordPress 4.1.
* Fixed a "Use of undefined constant ENT_HTML401" notice showing up on sites running PHP 5.3 or older.
* Fixed a double-escaping bug that could cause some link URLs to be displayed incorrectly.
* Updated French translation.
* Updated Dutch translation.

= 1.10.3 =
 * Security: Filter link URLs before displaying them on the "Broken Links" page.
 * Security: Prevent Editors and Administrators who don't have the "unfiltered_html" capability from creating "javascript:" URLs by editing existing links.

= 1.10.2 =
* Fixed an XSS vulnerability on the link checker settings page.
* Fixed old YouTube embed code parsing - now it should pick up self-closing embed tags without an `<object>` wrapper.
* Updated German translation.
* Updated Simplified Chinese translation.
* Link actions will now wrap properly on small screens.

= 1.10.1 =
* Fixed a database versioning issue that would cause multiple errors when upgrading from 1.9.5 to 1.10.

= 1.10 =
* Added a way to hide individual link actions like "Dismiss" and "Unlink".
* Added a "Fix redirect" link action. It replaces a redirect with a direct link. It is hidden by default and can be enabled through the settings page.
* Added a "Recheck" link action. Unlike the bulk action by the same name, it checks a link immediately and displays the results without having to refresh the page.
* Added a "Dismiss" bulk action.
* Added a note below the "link tweaks" settings explaining that they only apply to the contents of posts (and pages, and CPTs), not comments or custom fields.
* Made the "Redirect URL" column sortable.
* Added a "Details" link to the "Status" column.
* Added a "Warnings" section to Tools -> Broken Links. It shows problems that might be temporary or false positives. Warnings can be disabled through the settings page.
* Fixed a conflict with plugins that use PHP sessions.
* Fixed the "post statuses" option. Now disabling a post status (e.g. "Draft") should take effect immediately.
* Fixed the Mediafire link checker.
* Fixed the text in the "Status" column being slightly offset vertically when compared to other columns.
* Fixed search box position in WP 4.1-alpha.
* Added a few workarounds for situations where a custom post type is removed without first removing the posts.
* Removed the screen icon. WordPress has deprecated it.
* Other minor fixes.

= 1.9.5 =
* Fixed missing YouTube videos not being detected when the video URL starts with https instead of http.
* Enabled the YouTube video checker by default on new installations.
* Made the "dismiss link" option more permanent. Instead of restoring a dismissed link if the redirect URL changes even a little bit, the plugin will now ignore query string changes. This should fix many of the reports about dismissed links reappearing for no apparent reason.
* Updated Portuguese, German and Dutch translations.
* Other minor fixes.

= 1.9.4.2 =
* Updated Dutch translation again.
* Removed Bulgarian translation because it was poor quality and outdated.

= 1.9.4.1 =
* Updated Dutch translation.
* Updated POT file.

= 1.9.4 =
* Tested on WP 4.0 beta.
* Added a Serbo-Croatian translation.
* Added a Slovakian translation.
* Replaced the old Japanese translation with a new and more up-to-date version from a different translator.
* Updated Dutch, German, Polish, Hebrew and other translations.
* Fixed a notice about undefined index "status_text".
* Fixed a "doing it wrong" warning related to screen options.
* Fixed spurious false positives on links copied from Word or similar editors.
* Fixed view switcher appearance in WP 4.0.
* Replaced the deprecated like_esc() function with $wpdb->esc_like() where available.
* Fixed plaintext URLs not being detected if they're the very first thing in a post.
* Fixed a bug that caused quotes and other special characters in the broken link CSS and removed link CSS fields to be auto-escaped with a slash, potentially breaking the CSS.
* Fixed a bug that caused the "check custom fields" feature work inconsistently or not at all on custom post types.
* Fixed duplicate custom field links showing up when the user creates a revision with different field values.
* Fixed a specific type of false positive where some links would get flagged as "Unknown Error" and the log message would be "Empty reply from server".
* Fixed a bug where only the first enabled post type would be resynchronized during plugin activation.
* Added more logging.
* Removed Megavideo and MegaUpload modules. These sites no longer exist.

= 1.9.3 =
* Tested on WP 3.8.1 and WP 3.9-beta2.
* Added an option to sort links by link text. May produce unexpected results for links that have multiple copies with different anchor text.
* Added a Vietnamese translation.
* Added file-based logging for debugging purposes. Logging can be enabled in the "Advanced" section of the plugin settings page.
* Added a "Auto-Submitted: auto-generated" header to notification emails sent by the plugin. This should prevent "out-of-office" auto-responders and similar software from responding to these emails.
* Added domain-based rate limiting to the HTTP checker module.
* Throttled background parsing by about 40% to reduce overall resource usage.
* Fixed (probably) a long-standing bug related to encoding international characters in link URLs.
* Fixed a typo in the Polish translation.
* Made the error message that's displayed when trying to network-activate the plugin more useful.

= 1.9.2 =
* Fixed several UI/layout issues related to the new WP 3.8 admin style.
* Fixed HTML entity codes showing up in confirmation messages in when running a localized version of WP (only affects some languages).
* Fixed the "dismiss this notice" link URL not being HTML-escaped.
* Fixed a couple of cross-site scripting vulnerabilities related to the sort direction query argument not being properly validated and the bulk action form not escaping the current URL.
* Updated Hebrew translation.
* Updated French translation.
* When you dismiss a link, the dismissed link counter is now updated right away instead of on page reload.

= 1.9.1 =
* Updated Dutch, German, Chinese and Portuguese translations.
* Fixed suggestions not working on sites that force HTTPS in the WordPress admin.
* Tested on WordPress 3.7.

= 1.9 =
* Added the ability to edit link text from inside the plugin. This features is only available for certain types of links.
* Added a "suggestions" feature. When you go to edit a broken link, the plugin will now suggest replacing it with an archived page from the Wayback Machine (if available). You can disable suggestions in Settings -> Link Checker -> General.
* Added a Hebrew translation.
* Added support for HTML code in custom fields. To make the plugin treat a field as HTML, prefix its name with "html:" in BLC settings. For example, if you have a custom field named "foo" that contains HTML, enter it as "html:foo".
* Fixed: The "Status" column is now properly updated when editing a link.
* Fixed: Visual feedback when a link is successfully edited. Basically, it briefly changes the row background to green.
* Fixed: Email notifications will only include the "see all broken links here" link if the recipient can actually access that link.
* Fixed some UI labels not being localizable.
* The "Undismiss" action is now displayed in all views instead of only the "Dismissed" view. This way you can tell if a broken link has been dismissed without having to search the "Dismissed" list.
* Added information about the last email notification sent to debug info. It's accessible by clicking "show debug info" on the plugin settings page.

= 1.8.3 =
* Added a Hungarian translation.
* Fixed a bunch of "deprecated function" notices that showed up due to wpdb::escape() becoming deprecated in WP 3.6.
* Fixed a vulnerability that would allow users with the ability to bulk-edit links to execute arbitrary PHP code by using a specially crafted regex as the search string.
* Updated German translation.
* Replaced the old Dutch translation with a new and more complete translation by Robin Roelofsen.

= 1.8.2 =
* Removed one of the translator credits links because Google flagged it as "suspicious".
* Updated French translation.
* Updated Polish translation.
* Fixed several field size and layout issues that made the search form display incorrectly in Firefox.

= 1.8.1 =
* Updated the Polish and Simplified Chinese translations.
* Updated the German translation.
* Added translation strings for two modules that were missing them.
* Replaced a number of icons with GPL-compatible alternatives from Font Awesome.
* Removed some unused images.

= 1.8 =
* Added an option to only show the dashboard widget for users with the Administrator role, or to disable it completely.
* Added a way to change the notification email address.
* Added support for Smart YouTube "httpv://" links.
* Added support for protocol-relative URLs (for example, "//example.com/").
* Added support for checking YouTube playlists.
* Added a Brazilian Portuguese (pt-BR) translation.
* Updated Chinese (Traditional) translation.
* Switched over to storing plugin settings as JSON instead of serialized PHP arrays.
* Improved error reporting in situations where the plugin can not load settings from the database.
* Fixed: Display a more specific error message than just "update failed" when the plugin fails to modify a post. This only applies to WP 3.5+.
* Fixed: Display the right URL for embedded YouTube playlists. Previously the plugin would show the same (incorrect) URL for all playlists.

= 1.7.1 =
* Added an Arabic translation.
* Updated Portuguese translation.
* Updated French translation.
* Fixed MySQL errors caused by the plugin converting table names to lowercase.
* Fixed a bug where the plugin would sometimes report broken Twitter links as working.
* Fixed the plugin author URL.

= 1.7 =
* Added support for youtu.be shortlinks.
* Added a Finnish translation.
* Fixed a graphical bug where the currently selected settings tab would not be highlighted in WordPress 3.5.
* Removed the "Blogroll items" module from the list of link containers enabled by default. The WordPress developer team is planning to remove Link Manager from core, and the "Links" menu will be hidden by default in new WP 3.5 installs.
* Removed the Admin Menu Editor Pro ad from the "Settings -> Link Checker" and the "Tools -> Broken Links" pages.
* Disabled the news link (if any) for users who have donated.
* Removed support for pre-WP 2.9 post meta actions.
* Minor styling changes of screen meta links.
* Updated Danish, Germa, Italian, French and Simplified Chinese translations.
* Tested on WordPress 3.5.

= 1.6.2 =
* Another attempt to fix the "database not up to date" that some users are still experiencing even with 1.6.1.

= 1.6.1 =
* Fixed the "database not up to date" bug. Now the plugin should properly upgrade the DB.

= 1.6 =
* Added a way to dismiss links. Dismissed links don't show up in the "Broken" and "Redirects" lists, but are still checked as normal and get the normal link styles (e.g. strike-through for broken links). Useful if you want to, for example, acknowledge that a link is broken and leave it be.
* Added a "Redirect URL" column. For redirects this will display the URL that the link redirects to. For normal, non-redirected links, it will be empty. This column is hidden by default. You can enable it in the "Screen Options" panel.
* Updated French translation.
* Tested on WP 3.4.1.
* Replace the "More plugins..." link on the "Broken Links" page with a link to the Admin Menu Editor page. This link will be hidden for users who have donated.
* A number of minor fixes.

= 1.5.5 =
* Fix broken image on the settings page.

= 1.5.3 =
* Fixed a bug that would cause the donation flag to be recorded incorrectly. Apologies to everyone who donated.

= 1.5.2 =
* A few minor comment fixes.
* Move certain styles to a separate CSS file, which is where they belong.
* Replace the ThemeFuse banner with one from ManageWP (will go live on June 5).
* Instead of displaying several plugins in the "More plugins by Janis Elsts" box, sometimes display just one plugin (AME).

= 1.5.1 =
* Updated Portuguese translation.
* Updated German translation.
* Fixed the donation link to properly return to the Dashboard upon completion.
* Do not display ads to users who have donated.

= 1.5 =
* Added a FileServe checker.
* Added Turkish translation.
* Added GoogleVideo and Megavideo embed support.
* Fixed Megaupload links being reported with an "Unknown error" message when it should be "Not found".
* Fixed a couple of bugs in the Rapidshare and MediaFire checkers.
* Updated German translation.
* Updated Italian translation.
* Updated Portuguese translation.
* The explanatory text for the broken link CSS and removed link CSS inputs can now be translated.
* Tested on WP 3.4-alpha-20291.

= 1.4 =
* Added an option to send post authors notifications about broken links in their posts.
* Added the ability to sort links by URL (click the column header).
* Added YouTube API throttling to avoid going over the request quota, which could result in false positives on blogs with lots of YouTube videos.
* Added a Bulgarian translation.
* Updated Italian, German and Persian translations.
* Fixed a bug where the "Feedback" and other screen meta links wouldn't show up in WP 3.3.
* Fixed the tab CSS for the plugin settings page. Now they should be the right size and look the same in all modern browsers (tested in IE, Firefox, Chrome and Opera).
* Fixed drop-down arrows showing up on meta links that don't actually have dropdowns.
* Tested on WP 3.3 (RC2).

= 1.3.1 =
* Added support for the new YouTube embed code style. It needs to be explicitly enabled in options.
* Added credits link for the Persian language translator.
* Updated Portuguese translation.
* Updated German translation.
* Partial fix for Mediafire checker failing with a fatal error in some situations.

= 1.3 =
* Dropped PHP 4 support.
* Fixed a whole lot of PHP 5 related notices and strict-mode warnings.
* Fixed some inconsistent method declarations.
* Fixed a long-standing bug in the ver. 0.9.5 upgrade routine.
* Fixed the look and behavior of the "Feedback" and "Go to Broken Links/Go to Settings" links to be consistent with other WP screen meta links.
* Updated Chinese (TW) translation.
* Updated Portuguese translation.
* Updated Italian translation (minor fix).
* Replaced the link to FindBroken with a short list of (some of) my other plugins.

= 1.2.5 =
* Added Irish translation.
* Added Persian translation.
* Added Korean translation.
* Added Chinese Traditional translation.
* Updated German translation.
* Fixed (probably) missing diacritics in the Romanian translation.
* Fixed a crash bug caused by class-json.php no longer being present in the latest WP. Luckily, the plugin only really needed that class for backwards compatibility.
* Made the "database not up to date" error message a bit more helpful.
* Shortcodes in image URLs should work now.
* The Dashboard widget is no longer visible to non-privileged users.
* Replaced multiple instances of get_option('home') and get_option('siteurl') - both now deprecated - with home_url().

= 1.2.4 =
* Fixed a very stupid bug where links would be checked very slowly or not at all.
* Fixed the display of the news link.
* Updated Italian translation.

= 1.2.3 =
* Updated Portuguese translation.
* Updated German translation.
* Switched to a simpler, MySQL-based locking mechanism. Note: This may cause trouble for people who've hacked their WP install to use persistent database connections.
* Added a poll asking for feedback on a new BLC-related web application idea.
* Minor wording change in the debug info table.

= 1.2.2 =
* All Pro features now included in the free version!
* Updated Japanese translation.
* Updated Polish translation.
* Updated Portuguese translation.
* Added Romanian translation.
* Fixed a tab layout bug in IE 7.
* Fixed UTF-8 characters outside the domain name being encoded incorrectly (may only work with Curl).
* Fixed a missing translation in email notifications.
* Fixed a rare "only variables can be returned by reference" notice.
* Added a donation button and a MaxCDN ad to the Settings page.
* Added a "Go to Settings" button to the Broken Links page, and a "Go to Broken Links" button to the Settings page.
* Settings page now looks better on small screens.
* Email notifications are now enabled by default.
* "Link status" in the search form no longer defaults to the currently displayed filter/view.
* Made the "installation failed" message a bit more helpful.

= 0.9.7.2 =
* Added Polish translation.
* Updated Danish translation.
* Updated Italian translation.
* Fixed an uncommon "Cannot break/continue 1 level" error.
* Added a new user feedback survey (the link only shows up after you've used this version for at least two weeks).

= 0.9.7.1 =
* Updated German translation and fixed the corresponding credits link.

= 0.9.7 =
* Allow custom field names with spaces.
* Updated German translation.
* Updated Portuguese translation
* Made the "Current load" label localizeable.
* Fixed a translation-related bug where the various checkboxes in the "Link types" and "Look for links in" sections would appear in English even when a valid translation was available.
* Fixed non-ASCII URLs being mangled when links are automatically marked with the "broken_link" CSS class.
* Fixed blog names that include quotes being displayed incorrectly in email notifications.
* When removing a link via the "Unlink" action, add the old URL as the title attribute of the now-unlinked anchor text.
* When resolving relative URLs posted in comments, use the comment's permalink as the base (previously the blog's homepage URL was used).

= 0.9.6 =
* Updated Danish translation.
* Updated Italian translation.
* Updated Portuguese translation
* Fixed incorrect parsing of relative URLs that consist solely of a query string or \#fragment.
* Fixed superfluous resynchronization requests being issued when the plugin is re-activated.
* Fixed only one of character set and collation being specified for the plugin's tables.
* Added default status text for HTTP codes 509 and 510.
* Added the installation log to debug info output.
* Added lots of logging to routines called on activation.
* Added an "Upgrade to Pro" button to the plugin's pages.
* Removed the highlight on the "Feedback" button.
* Fail fast if trying to activate on an unsupported version of WordPress.
* Ensure PHP and browser timeouts don't prematurely terminate the installation/upgrade script.
* Plugin JavaScript and CSS files are now loaded using HTTPS when FORCE_ADMIN_SSL is on.

= 0.9.5 =
* Added the ability to check scheduled, draft and private posts.
* Added a way to individually enable/disable the monitoring of posts, pages, comments, the blogroll, and so on.
* New "Status" column in the "Broken Links" table.
* Visible table columns and the number of links per page can now be selected in the "Screen Options" panel.
* Replaced the "Delete sources" action with "Move sources to Trash" (except on blogs where Trash is disabled).
* New URL editor interface, now more consistent with the look-n-feel of the inline editor for posts.
* New status icon to help distinguish "maybe broken" and "definitely broken" links.
* Tweaked table layout - links first, posts/etc last.
* Added "Compact" and "Detailed" table views (for now, the differences are quite minor).
* Split the settings page into several tabs.
* Removed the "Details" links as redundant. To display link details, click the contents of the "Status" or "Link text" columns instead.
* Added a way to individually enable/disable the monitoring of various link types, e.g. HTML links, images, etc.

= 0.9.4.4 =
* Fixed "Edit URL" and "Unlink" not working on PHP4 servers.

= 0.9.4.3 =
* Another PHP 4 fix. Considering dropping support for PHP4 in light of the counterintuitive workarounds required to make perfectly valid PHP5 code work in that ghastly thing.
* Added a partial workaround for sites that have use strange DB\_CHARSET settings.

= 0.9.4.2 =
* Added more debugging data to the "Show debug info" table.
* Added missing indexes to the instance table.
* Yet more PHP4 compatibility fixes.
* Added a notification bubble with the current number of broken links to the Tools -> Broken Links menu item.

= 0.9.4.1 =
* Fixed PHP 4 incompatibilities introduced in the previous release.
* Fixed bulk unlink.
* Updated Italian translation.
* Updated Danish translation.

= 0.9.4 =
* Fixed missing post and comment edit links in email notifications.
* Updated Danish translation.
* Added Japanese translation.
* Added a Hindi translation.
* Added a Portuguese translation.
* Slightly improved DB error reporting.
* Added the ability to disable comment link checking.
* Fixed a couple of minor bugs that made some of the UI text impossible to translate.
* The plugin's tables are now created with the same character set and collation settings as native WP tables (previously they used the database defaults instead).
* Automatically clean up and optimize the plugin's tables twice per month.
* Instead of displaying a zero response time for timed out links, now it shows how long the plugin waited before assuming that the link has timed out.
* Added the default PHP script execution time limit to the "Debug info" table.
* Added a "Mark as not broken" bulk action.
* Links that make the plugin crash are no longer assumed to be broken.

= 0.9.3 =
* Fixed a JS error that only happened in IE by removing a superfluous comma from an object literal.
* Fixed load limiting not being completely disabled on servers that don't support it.
* Fixed a mishandling of new comments that would occur when CAPTCHA verification was enabled and someone entered an incorrect code.
* Added installation/update logging.
* Fixed a crash that would occur when the user tried to permanently delete a trashed post that has comments.

= 0.9.2 =
* In Tools -> Broken Links, highlight links that have been broken for a long time (off by default).
* Fixed an invalid parameter bug in the HTTP link checking routine.
* Added nofollow to broken links (optional, only works for links in posts).
* Fixed some PHP notices and a bunch of deprecated function calls.
* Fixed "Trash" links for comments.

= 0.9.1 =
* Fixed the "syntax error: unexpected $end" problem caused by a unintentional PHP shorttag.
* Eliminated a bunch of false positives by adding a workaround for buggy servers that incorrectly respond with 404 to HEAD requests.
* Increased the default server load limit to 4.0 to prevent the plugin from idling endlessly on weakling servers.

= 0.9 =
* Masquerade as IE 7 when using the Snoopy library to check links. Should prevent some false positives.
* Fixed relative URL handling (yet again). It'll work this time, honest ;)
* Fixed post titles being displayed incorrectly on multilingual blogs (props Konstanin Zhilenko)
* Misc fixes/comments.
* "Unlink" works properly now.
* Additional source code comments.
* Don't try to display icons in email notifications. It didn't work anyway.
* Use AJAX nonces for additional security.
* General code cleanup.
* Email notifications about broken links.
* "Recheck" bulk action.
* Check comment links.
* Suspend checking if the server is overloaded (on by default).
* Icons for broken links and redirects.
* Fixed some UI glitches.
* "Discard" gone, replaced by "Not broken".
* "Exclude" gone from action links.
* Better handling of false positives.
* FTP, mailto:, javascript: and other links with unsupported protocols now show up in the �All links� list.

= 0.8.1 =
* Updated Italian translation.
* Removed the survey link.

= 0.8 =
* Initial support for performing some action on multiple links at once.
* Added a "Delete sources" bulk action that lets you delete all posts (or blogroll entries) that contain any of the selected links. Doing this in WP 2.9 and up this will instead move the posts to the trash, not delete them permanently.
* New bulk action : Unlink. Removes all selected links from all posts.
* New bulk action : Fix redirects. Analyzes the selected links and replaces any redirects with direct links.
* Added a notice asking the user to take the feedback survey.
* Update the .POT file with new i18n strings.

= 0.7.4 =
* Fixed a minor bug where the plugin would display an incorrect number of links in the "Displaying x-y of z" label when the user moves to a different page of the results.
* Added Ukrainian translation.

= 0.7.3 =
* Reverted to the old access-checking algorithm + some error suppression.

= 0.7.2 =
* Only use the custom access rights detection routine if open\_basedir is set.

= 0.7.1 =
* Updated Russian translation.
* Yet another modification of the algorithm that tries to detect a usable directory for the lockfile.

= 0.7 =
* Added a Search function and the ability to save searches as custom filters
* Added a Spanish translation
* Added a Belorussian translation
* Added an option to add a removed\_link CSS class to unlinked links
* Slight layout changes
* Added localized date display (where applicable)
* The background worker thread that is started up via AJAX will now close the connection almost immediately after it starts running. This will reduce resource usage slightly. May also solve the rare and mysterious slowdown some users have experienced when activating the plugin.
* Updated Italian translation
* Fixed an unlocalized string on the "Broken Links" page

= 0.6.5 =
* Added Russian translation.

= 0.6.4 =
* Added French translation.
* Updated Italian translation.

= 0.6.3 =
* Added a German translation.

= 0.6.2 =
* Added an Italian translation.
* Added a Danish translation.
* Added a Chinese (Simplified) translation.
* Added a Dutch translation.

= 0.6.1 =
* Some translation-related fixes.

= 0.6 =
* Initial localization support.

= 0.5.18 =
* Added a workaround for auto-enclosures. The plugin should now parse the "enclosure" custom field correctly.
* Let people use Enter and Esc as shortcuts for "Save URL" and "Cancel" (respectively) when editing a link.

= 0.5.17 =
* Added a redirect detection workaround for users that have safe\_mode or open\_basedir enabled.

= 0.5.16.1 =
* Be more careful when parsing safe\_mode and open\_basedir settings.

= 0.5.16 =
* Also try the upload directory when looking for places where to put the lockfile.

= 0.5.15 =
* Editing links with relative URLs via the plugin's interface should now work properly. Previously the plugin would just fail silently and behave as if the link was edited, even if it wasn't.

= 0.5.14 =
* Made the timeout value used when checking links user-configurable.
* The plugin will now report an error instead of failing silently when it can't create the necessary database tables.
* Added a table listing assorted debug info to the settings page. Click the small "Show debug info" link to display it.
* Cleaned up some redundant/useless code.

= 0.5.13 =
* Fixed the bug where the plugin would ignore FORCE\_ADMIN\_SSL setting and always use plain HTTP for it's forms and AJAX.

= 0.5.12 =
* Let the user set a custom temporary directory, if the default one is not accessible for some reason.

= 0.5.11 =
* Use absolute paths when loading includes. Apparently using the relative path could cause issues in some server configurations.

= 0.5.10.1 =
* Fix a stupid typo

= 0.5.10 =
* Separated the user-side functions from the admin-side code so that the plugin only loads what's required.
* Changed some internal flags yet again.
* Changed the algorithm for finding the server's temp directory.
* Fixed the URL extraction regexp again; turns out backreferences inside character classes don't work.
* Process shortcodes in URLs.
* If the plugin can't find a usable directory for temporary files, try wp-content.
* Don't remove <pre> tags before parsing the post. Turns out they can actually contain valid links (oops).

= 0.5.9 =
* Added an autogenerated changelog.
* Added a workaround to make this plugin compatible with the SimplePress forum.
* Fixed <pre> block parsing, again.
* Fixed a bug where URLs that only differ in character case would be treated as equivalent.
* Improved the database upgrade routine.

= 0.5.8.1 =
* Added partial proxy support when CURL is available. Proxies will be fully supported in a later version.

= 0.5.8 =
* Fixed links that are currently in the process of being checked showing up in the "Broken links" table.
* The post parser no longer looks for links inside <pre></pre> blocks.

= 0.5.7 =
* Slightly changed the dashboard widget's layout/look as per a user's request.

= 0.5.6 =
* Improved relative URL parsing. The plugin now uses the permalink as the base URL when processing posts.

= 0.5.5 =
* Minor bugfixes
* URLs with spaces (and some other special characters) are now handled better and won't get marked as "broken" all the time.
* Links that contain quote characters are parsed properly.

= 0.5.4 =
* Fixed the uninstaller not deleting DB tables.
* Other uninstallation logic fixes.

= 0.5.3 =
* Improved timeout detection/handling when using Snoopy.
* Set the max download size to 5 KB when using Snoopy.
* Fixed a rare bug where the settings page would redirect to the login screen when saving settings.
* Removed some stale, unused code (some still remains).

= 0.5.2 =
* Fixed a SQL query that had the table prefix hard-coded as "wp\_". This would previously make the plugin detect zero links on sites that have a different table prefix.

= 0.5.1 =
* Fix a bug when the plugin creates a DB table with the wrong prefix.

= 0.5 =
* This is a near-complete rewrite with a lot of new features.
* See �http://w-shadow.com/blog/2009/05/22/broken-link-checker-05/ for details.

= 0.4.14 =
* Fix false positives when the URL contains an #anchor

= 0.4.13 =
* (Hopefully) fix join() failure when Snoopy doesn't return any HTTP headers.

= 0.4.12 =
* *There are no release notes for this version*

= 0.4.11 =
* Set the Referer header to blog's home address when checking a link. This should help deal with some bot traps.
* I know, I know - there haven't been any major updates for a while. But there will be eventually :)
* Fix SQL error when a post is deleted.

= 0.4.10 =
* Changed required access caps for "Manage -> Broken Links" from manage\_options to edit\_ohers\_posts. This will allow editor users to access that page and it's functions.

= 0.4.9 =
* Link sorting, somewhat experimental.
* JavaScript sorting feature for the broken link list.

= 0.4.8 =
* CURL isn't required anymore. Snoopy is used when CURL isn't available.
* Post title in broken link list is now a link to the post (permalink). Consequently, removed "View" button.
* Added a "Details" link. Clicking it will show/hide more info about the reported link.
* "Unlink" and "Edit" now work for images, too. "Unlink" simply removes the image.
* Database modifications to enable the changes described above.
* Moved the URL checking function from wsblc\_ajax.php to broken-link-checker.php; made it more flexible.
* New and improved (TM) regexps for finding links and images.
* A "Settings" link added to plugin's action links.
* And probably other stuff I forgot!
* Grr :P

= 0.4.7 =
* Minor enhancements :
* Autoselect link URL after the user clicks "Edit".
* Make sure only HTTP and HTTPS links are checked.
* More substantive improvements will hopefully follow next week.

= 0.4.6 =
* Minor compatibility enhancement in wsblc\_ajax.php - don't load wpdb if it's already loaded.

= 0.4.5 =
* Bugfixes. Nothing more, nothing less.
* Revisions don't get added to the work queue anymore.
* Workaround for rare cURL timeout bug.
* Improved WP 2.6 compatibility.
* Correctly handle URLs containing a single quote '.

= 0.4.4 =
* Consider a HTTP 401 response OK. Such links won't be marked as broken anymore.

= 0.4.3 =
* Fix : Don't check links in revisions, only posts/pages.

= 0.4.2 =
* *There are no release notes for this version*

= 0.4.1 =
* Split translated version from the previous code. Was causing weird problems.

= 0.4-i8n =
* *There are no release notes for this version*

= 0.4 =
* Added localization support (may be buggy).

= 0.3.9 =
* Fix : Use get\_permalink to get the "View" link. Old behavior was to use the GUID.

= 0.3.8 =
* Edit broken links @ Manage -> Broken Links (experimental)

= 0.3.7 =
* Change: A bit more verbose DB error reporting for the "unlink" feature.

= 0.3.6 =
* Switch from wp\_print\_scripts() to wp\_enqueue\_script()
* Wp\_enqueue\_script()

= 0.3.5 =
* New: "Delete Post" option.
* New: Increase the compatibility number.
* Change: Default options are now handled in the class constructor.

= 0.3.4 =
* Ignore mailto: links
* Ignore links inside <code> blocks

= 0.3.3 =
* *There are no release notes for this version*

= 0.3.2 =
* Fix Unlink button not working, some other random fixes

= 0.3.1 =
* *There are no release notes for this version*

= 0.3 =
* *There are no release notes for this version*

= 0.2.5 =
* Applied a small patch @ 347
* Fix some omissions
* Lots of new features in version 0.3

= 0.2.4 =
* Bigfix - use GET when HEAD fails

= 0.2.3 =
* MySQL 4.0 compatibility + recheck\_all\_posts function

= 0.2.2.1 =
* *There are no release notes for this version*

= 0.2.2 =
* *There are no release notes for this version*

= 0.2 =
* *There are no release notes for this version*

= 0.1 =
* *There are no release notes for this version*

== Upgrade Notice ==

= 1.10.5 =
Fixes a significant security issue.

= 1.9.2 =
Fixes UI issues related to the new WP 3.8 admin style and a few security vulnerabilities.

= 1.6.2 =
Attempts to fix the "database not up to date" bug that some users are still experiencing with 1.6.1. If you have not encountered this bug, you can skip this update.

= 1.4 =
Adds an option to send post authors notifications about broken links in their posts and the the ability to sort links by URL, as well as a number of other updates and fixes.

= 0.9.4.2 =
Fixes a major PHP4 compatibility problem introduced in version 0.9.4 and adds a notification bubble with the current broken link count to the "Broken Links" menu.
