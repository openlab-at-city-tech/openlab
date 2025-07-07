=== XPoster - Share to Bluesky and Mastodon ===
Contributors: joedolson
Donate link: https://xposterpro.com
Tags: bluesky, post, social, sharing, mastodon
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 7.4
License: GPLv2 or later
Text Domain: wp-to-twitter
Stable tag: 5.0.4

Posts to Bluesky, Mastodon or X when you update your WordPress blog or add a link, with your chosen URL shortening service.

== Description ==

= Post Updates from WordPress to Bluesky, Mastodon, and X. =

* New in version 4.3: Bluesky support.
* New in version 4.2: Mastodon support.

XPoster is a time-saving tool for keeping your social media accounts up to date with news and posts from your site.

XPoster uses customizable status templates for updates sent when updating or editing posts, pages, or custom post types. You can customize your messages for each post, using custom template tags to generate the status update. 

= Free Features =

* Support for Bluesky, Mastodon, and X.
* Use post tags as hashtags
* Use alternate URLs in place of post permalinks
* Support for Google Analytics
* Support for XMLRPC remote clients
* Use [YOURLS](https://yourls.org), [Bit.ly](https://wordpress.org/plugins/codehaveli-bitly-url-shortener/), [jotURL](https://joturl.com), or [Hum](https://wordpress.org/plugins/hum/) as external URL shorteners.
* Rate limiting: make sure you don't exceed X.com's API rate limits. 

= Premium Features in [XPoster Pro](https://xposterpro.com) =

* Authors can set up their own accounts in their profiles
* Time delayed status updates
* Scheduled update management
* Simultaneously post updates to site and author accounts
* Preview and send status updates for comments
* Filter updates by taxonomy (categories, tags, or custom taxonomies)
* Upload images with alt attributes
* Integrated Card support
* Support for Player Cards with integrated captions where supported
* Automatically schedule updates from old posts

Want to stay up to date on XPoster? [Follow me on Bluesky!](https://bsky.app/profile/joedolson.bsky.social) or [Follow me on Mastodon!](https://toot.io/@joedolson)

= Translations =

Visit the [XPoster translation site](https://translate.wordpress.org/projects/wp-plugins/wp-to-twitter/stable) to see how complete the current translations are.

Translating my plug-ins is always appreciated. Work on XPoster translations at <a href="https://translate.wordpress.org/projects/wp-plugins/wp-to-twitter">the WordPress translation site</a>! You'll need a WordPress.org account to contribute!

= Extending XPoster =

Check out my <a href="https://github.com/joedolson/plugin-extensions/tree/master/wp-to-twitter">GitHub repository of plug-in extensions</a>.

== Changelog ==

= 5.0.4 =

* Support change for Bluesky images (XPoster Pro).
* Remove obsolete custom plugin update notice.
* Highlight premium features in metabox.

= 5.0.3 =

* Bug fix: Remove some unused CSS.
* Bug fix: CSS issue conflicting with other plugins. Props @reiniggen.
* Bug fix: Ensure that exact template passed is used in cases where testing a template is needed.
* Bug fix: Missing service length limits in character counter options.
* Bug fix: Wrap text in displayed log after AJAX post.
* Change: Display characters used rather than characters left in character counter.

= 5.0.2 =

* Bug fix: X user connection include an extra nonce, breaking user settings.
* Bug fix: User styles not enqueued on user edit profile screen.
* Bug fix: Simplify HTML and fix mis-nested `div`. 
* Bug fix: Fix duplicate IDs on disconnect checkboxes.

= 5.0.1 =

* Bug fix: Remove whitespace from status update template shown for non-admins.
* Bug fix: Improve layout of settings message for non-admins.
* Bug fix: Incorrectly nested closing `div` broke classic metabox layout for non-admins.

= 5.0.0 =

* Feature: Ability to disable connected services without disconnecting.
* Feature: Select which services you wish to send a given update to.
* Feature: Pass custom update text for each connected service.
* Feature: Define an excerpt length unique to each service.
* Change: Extend default excerpt length.
* Change: Always defer template execution until status update is sent.
* Change: Make character counting messages indicate which service limit is reached.
* Change: simplify #account# and #@# handlers.
* Bug fix: Make AJAX submissions aware of more metabox settings.
* Improved error handling.
* Design updates.
* Move XPoster Pro specific code into XPoster Pro package.
* More code documentation.
* Update references to Tweets to be service independent.
* Significant structural reorganization.
* Change output of #account# and #@# template tags for increased predictability.
* Code restructure to group service-specific code.

= 4.3.2 =

* Bug fix: Failed to update one version number.
* Bug fix: Github icon should be white.
* Accessibility: toggle buttons were not buttons.
* Compat: Move Pro-only JS & Styles into pro.
* Docs: Document UTM filters.
* Docs: Update language to match documentation & expectation.

= 4.3.1 =

* Bug fix: Custom update text not displayed after saving, causing re-save to delete.

= 4.3.0 =

* Add support for Bluesky.
* Misc. improvements to handling of multiple services.
* Misc. improvements to UI.
* Minor debugging improvements.
* Bug fix: Avoid JS errors if metabox not enqueued.
* Encode backup title in same manner as primary title.
* Improve coverage of documented filters.
* Removed some unused functions.
* Simplified publishing logic.
* Only upload images to service if is one of that service's supported mime types.

= 4.2.6 =

* Remove textdomain loader (obsolete since WP 4.6)
* Bug fix: HTML encode title sent to X API when using backup title value.

= 4.2.5 =

* Bug fix: Fatal error thrown on PHP 8+ if removing stray characters from tag boundaries due to obsolete argument usage in `mb_strrpos()`. Props @toru.
* Bug fix: Update classes to allow dynamic properties following PHP 8.2 dynamic prop deprecation.
* Bug fix: If template tags were re-ordered in the `wpt_tags` filter, they were not also reordered in values.
* Change: Add fallback call to post title if not in post info array.
* Filter: add `wpt_custom_tag` filter to manage the value of custom tags added in `wpt_tags`.

= 4.2.4 =

* Bug fix: Debugging timestamps saved as microtime but read as time.
* Bug fix: Add selected shortener to debugging info.
* Bug fix: Verify that last status is an array & return unrecognized error message if not identifiable.
* Change: Add $get_url parameter to `wpt_shorten_url` to explicitly determine whether existing short URLs are fetched.
* Feature: Status update template tag buttons in editor.

= 4.2.3 =

* Bug fix: Update deprecated JS.
* Bug fix: Handle case if connection response is not valid JSON.
* Bug fix: Minor improvements to CSS & JS.
* Update tested to & copyrights.

= 4.2.2 =

* Bug fix: Last Tweet notice improperly called array. Props @mattyrob, @pyro-code01.
* Bug fix: Only show upgrade notice if it's populated in the readme.
* Change: Change scripts to register separately from enqueuing.
* Change: Change Pro filters to a filter instead of a direct function call.
* Tooling: Update to PHPCS 3.

= 4.2.1 =

* Bug fix: All notices used error class, regardless of actual status.
* Bug fix: Add a notice when a request exception occurs.
* Bug fix: Misnamed variable in Mastodon authentication process.
* Mention Mastodon in plugin name.

= 4.2.0 =

* Add support for posting to Mastodon instances.
* Bug fix: If category and category description not defined, PHP warning thrown.
* Bug fix: Make tabbed navigation use a `nav` element for improved accessibility.
* Bug fix: Media heading could show in meta box when no media options are enabled.
* Bug fix: Should not show bearer token message on profile page if user settings not enabled.
* Text changes: Change references to "Twitter" and "Tweet".
* Docs: Improve and add some additional hook documentation.

= 4.1.2 =

* Bug fix: Checkbox marked as checked in plugin settings if parent array exists.
* Bug fix: Fix PHP notice when link manager not enabled.
* Change: Use wp_rand to generate random integer instead of mt_rand.

= 4.1.1 =

* Debugging improvements.
* Show update to Miscellaneous settings checkboxes immediately. Props @mt8.

= 4.1.0 =

* Bug fix: X.com upload endpoint doesn't support gif; disable upload if gif.
* Bug fix: Fix screen name comparisons so Pro scripts load correctly.
* Bug fix: Save default Tweet length option so character count uses correct value.
* Update: override block editor sidebar padding.
* Update: Combine separate admin script registrations into one function.
* Update: Add versions to registered scripts and styles.
* Update: Remove tab interface in post sidebar.
* Update: Add default template to tweet text box as placeholder.
* Update: Change default excerpt length to 60.
* Update: Add missing sales link & update docs link.
* Update: Remove obsolete FAQ question.

= 4.0.3 =

* Remove X.com feed and search widgets.
* Improve error message reporting.

= 4.0.2 =

* Bug fix: Need to offer users the option to either disconnect or add a bearer token.

= 4.0.1 =

* Prefix vendor classes for better cross compatibility. Props [Robert Korulczyk](https://github.com/rob006)
* Improved catching of Exceptions.
* Remove deprecated CURL arg.
* Only show bearer token admin notice if user already authenticated.

= 4.0.0 =

* Rebranding to XPoster.
* Update to use X.com API v2. (https://github.com/noweh/twitter-api-v2-php)
* Those two things encompass a huge amount of change.

== Installation ==

1. Upload the `wp-to-twitter` folder to your `/wp-content/plugins/` directory
2. Activate the plugin using the `Plugins` menu in WordPress
3. Go to Settings > XPoster
4. Adjust the XPoster Options as you prefer them. 
5. Create a Bluesky, Mastodon, or X application and configure your application with XPoster.

== Frequently Asked Questions ==

= Why are you still calling things Tweets? =

As of version 5.0.0, I'm not. But if you see something still referencing a Tweet, let me know it! 

= Do I need to pay for an API plan at X.com? =

If you're publishing more than about 50 Tweets a day, you'll need to pay for a premium API plan. This is out of my control.

= X.com's Application creation process is very difficult. Why do I have to do this? =

XPoster has always followed the principle that you are the owner of your own application. Many other applications require you to pass your data through a 3rd party that you authenticate to post to Twitter. With the new API policies at X.com, this is a significant benefit to most users. X.com allows up to 1500 Tweets per month on their free API plan, which is practical for most independent sites. 

== Screenshots ==

1. XPoster Set up.
2. XPoster Post Meta box.
3. XPoster post meta box with XPoster PRO active.
4. XPosterPro settings.
5. Basic XPoster Settings

== Upgrade Notice ==
