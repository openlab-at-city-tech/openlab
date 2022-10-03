=== WP to Twitter ===
Contributors: joedolson
Donate link: https://www.joedolson.com/donate/
Tags: twitter, microblogging, bitly, yourls, redirect, shortener, post, links, social, sharing, media, tweet
Requires at least: 4.9.8
Tested up to: 6.0
Requires PHP: 5.6
License: GPLv2 or later
Text Domain: wp-to-twitter
Stable tag: 3.6.2

Posts a Twitter update when you update your WordPress blog or add a link, with your chosen URL shortening service.

== Description ==

= Post Tweets from WordPress to Twitter. =

Yep. That's the basic functionality. But it's not the only thing you can do:

* Display your Recent Tweets: Widget for your recent Tweets. Fetch Tweets from your own or any other account.
* Display Tweets based on a search: Display the Tweets resulting from a search and limit by Geolocation.
* Shorten URLs in your Tweets with popular URL shorteners, or let Twitter to do it with [t.co](http://t.co). 

[Upgrade to WP Tweets Pro](http://www.joedolson.com/wp-tweets-pro/) and schedule Tweets, set up automatic reposts, upload images and more!

WP to Twitter uses a customizable Tweet template for Tweets sent when updating or editing posts and pages or custom post types. You can customize your Tweet for each post, using custom template tags to generate the Tweet. 

= Free Features =

* Use post tags as Twitter hashtags
* Use alternate URLs in place of post permalinks
* Support for Google Analytics
* Support for XMLRPC remote clients
* Use [YOURLS](https://yourls.org), [Bit.ly]you(https://wordpress.org/plugins/codehaveli-bitly-url-shortener/), [jotURL](https://joturl.com), or [Hum](https://wordpress.org/plugins/hum/) as external URL shorteners.
* Rate limiting: make sure you don't exceed Twitter's API rate limits. 

= Premium Features =

[Upgrade to WP Tweets Pro](https://www.joedolson.com/wp-tweets-pro/) for extra features, including:

* Authors can set up their own Twitter accounts in their profiles
* Time delayed Tweeting
* Scheduled Tweet management
* Simultaneously Tweet to site and author Twitter accounts
* Preview and Tweet comments
* Filter Tweets by taxonomy (categories, tags, or custom taxonomies)
* Upload images to Twitter with alt attributes
* Integrated Twitter Card support
* Support for Player Cards with integrated captions
* Automatically schedule Tweets of old posts
* [Get a license for WP Tweets PRO!](https://www.joedolson.com/wp-tweets-pro/)

Want to stay up to date on WP to Twitter? [Follow me on Twitter!](https://twitter.com/joedolson)

= Translations =

Visit the [WP to Twitter translation site](https://translate.wordpress.org/projects/wp-plugins/wp-to-twitter/stable) to see how complete the current translations are.

Translating my plug-ins is always appreciated. Work on WP to Twitter translations at <a href="https://translate.wordpress.org/projects/wp-plugins/wp-to-twitter">the WordPress translation site</a>! You'll need a WordPress.org account to contribute!

= Extending WP to Twitter =

Check out my <a href="https://github.com/joedolson/plugin-extensions/tree/master/wp-to-twitter">GitHub repository of plug-in extensions</a>.

== Changelog ==

= 3.6.2 =

* Bug fix: YOURLS returns a 400 error if a URL is re-submitted, but WP to Twitter only handled JSON object if a 200 was returned.
* Bug fix: Provide context labels for wp_die and die calls
* Bug fix: Hum shortener threw errors if you attempted to shorten a link, since it only works for posts.
* Bug fix: Missing sanitization in AJAX Tweet requests from admin.
* Bug fix: Correctly handle YOURLS url shortening requests when there is already an existing shortened URL for that path.

= 3.6.1 =

* Bug fix: Don't expect a nonce or attempt to handle post meta if post not submitted from WordPress admin.

= 3.6.0 =

* Bug fix: Fix incorrect textdomains.
* Security: Missing nonce verification in metabox.
* Change: New filter `wpt_postpone_rendering` to allow Pro to delay template rendering.
* Docs: Start adding hook docs - https://joedolson.github.io/wp-to-twitter/. Work in progress.

= 3.5.6 =

* Bug fix: Change in link to Twitter API error codes.
* Bug fix: Restore sales page, accidentally removed in previous update.
* Change: Option added to enable debugging from admin (in Advanced Settings.)

= 3.5.5 =

* Change: Twitter app setup instructions updated.
* Removed: URL data migration. Any data that needs migrating should be long completed.

= 3.5.4 =

* Change: Twitter help/configuration endpoint retired. Values changed to static.
* Change: Misc. UI updates.
* Bug fix: Re-query YOURLS endpoint if format throws no-match result.
* Allow enabling debugging without Pro.
* Log Tweet status IDs.

= 3.5.3 =

* Bug fix: Query to Twitter config endpoint should never run more than once a day.

= 3.5.2 =

* Bug fix: missing CSS for headings.
* Bug fix: Add option to allow autoposting to work. Option will break manual posting, however.

= 3.5.1 =

* Bug fix: Run metadate save on wp_after_insert_post, as well, when it exists.
* Bug fix: Incorrect value passed to wp_localize_script.
* Move PHP tests from Travis-CI to GitHub Actions.
* Fix results of PHP testing.
* Minor clean-up in debugging.
* Update screenshots for repository.
* Changed WP requirement to >= 4.9.8
* Bug fix: Post meta saving after post published in block editor

= 3.5.0 =

* Update connection instructions to match Project & App structure at Twitter.
* Update metabox design and layout.
* Toggle default length based on current locale.
* Remove stored URLs; no longer important to avoid repeat shortener queries.
* Mask app tokens after saving.
* Automatically switch to staging mode if environment query returns staging.
* Bug fix: remove whitespace in some settings inputs
* New: use wp_after_insert_post action (new in WP 5.6) so terms & metadata are saved at the time posts are published when using the block editor.
* Numerous updates for WP Tweets Pro users.

= 3.4.10 =

* Feature: ability to add mentions in tags to be parsed with @ instead of #.
* Feature: Add support for <a href="https://wordpress.org/plugins/hum/">Hum URL shortener</a>
* Improve install process for 3rd-party plugin URL shorteners.
* Use Full post modified & publish dates in debugging info. (Including seconds.)

= 3.4.9 =

* Clear a couple PHP notices.
* Add styles for Pro.

= 3.4.8 =

* Bug fix: Incorrect variable type in default tab assignment.
* Bug fix: Two incorrectly named array keys in debugging.
* Bug fix: Shortened URLs shouldn't be called if using WP permalinks.

= 3.4.7 =

* Bug fix: Changed rules for differentiating between new and edited posts.
* Bug fix: Bit.ly supporting plug-in changed function name.
* Bug fix: Variable types different between default settings & saved settings.
* Bug fix: Prevent scheduled Tweets (Pro) if post has been deleted.
* Update debugging messages & data for better clarity.

= 3.4.6 =

* Bug fix: YOURLS queries not executing.

= 3.4.5 =

* Add support for @ references on tags.
* Add support for <a href="https://wordpress.org/support/plugin/codehaveli-bitly-url-shortener/">Codehaveli Bitly URL Shortener</a>
* Remove deprecated category filters. (UI disabled in 2014.)
* Don't query shorteners if they are enabled but settings are missing.

= 3.4.4 =

* Bug fix: Due to external add-ons, need to test URL shortener settings as strings, not integers.
* Bugfix: If YOURLS JSON object does not exist, it cannot have values.
* Change: Support custom domains in jotURL.
* Change: Add user info to debugging records.

= 3.4.3 =

* Bug fix: Failed to account for a URL value that could be false in template parsing.

= 3.4.2 =

* Bug fix: don't parse Username settings if user is connected to Twitter (Pro)
* Bug fix: Non-semantic text in user settings.
* Bug fix: type error in media comparison.
* Improve logic for exiting media handling. (Pro)

= 3.4.1 =

* Removed goo.gl shortener completely (disabled by Goo.gl in March 2019)
* Removed su.pr shortener completely (Stumbleupon closed down in June 2018)
* Prep for removal of Bit.ly URL shortener. (Bitly API v3 will shut down March 2020)
* Misc. markup improvements.

= 3.4.0 =

* New function: wpt_allowed_post_types(). Returns array of post types that can be Tweeted.
* New template tag: #categories# Return string of all categories on post. Filterable with 'wpt_twitter_category_names'
* Change: default tag length allowed raised to 20
* Change: default number of tags allowed raised to 4
* Breaking change: Remove major function deprecated in January 2017 and minor functions deprecated March 2018.

= 3.3.12 =

* Missed ssl_verify=false removed

= 3.3.11 =

* Pass post ID to wpt_retweet_text filter.
* Don't throw duplicate Tweet error if Tweet is blank.

= 3.3.10 =

* Change: Display UI for post types that are private but have a UI (e.g., WooCommerce coupons)
* Bug fix: User permissions for connecting to Oauth overrode ability to enter personal settings.
* Bug fix: Exit meta migration if post does not exist.

= 3.3.9 =

* Added filter to cancel Tweets for custom reasons after all other filters executed.
* Removed video on app creation, due to Twitter's radical revision of creation process.
* Update setup instructions inside app.

= 3.3.8 =

* Change function name for checking edit vs. new for clarity.
* Update debugging function to pass post ID of current Tweet.
* Bug fix: PHP Notice in settings.
* Bug fix: If rate limiting cron not set, automatically recreate.

= 3.3.7 =

* Change: Remove replacement character setting unless in use for non-space character
* Change: Capitalize each word in tags sent to Twitter (accessibility)

= 3.3.6 =

* Bug fix: Check for existing short URL should not run when parsing text of Tweets for URLs.

= 3.3.5 =

* Bug fix: Assignment replaced with comparison in connection creation.

= 3.3.4 =

* Bug fix: fallback normalizer method called incorrectly

= 3.3.3 =

* Removed: upgrade paths from version 2.4.x
* Removed: support for YOURLS version 1.3
* Removed: support for Twitter Friendly Links (plug-in not updated in 8 years)
* Removed: Ability to enable the Goo.gl URL shortener (see: https://developers.google.com/url-shortener/)
* Removed: fallback functions required for PHP 4 support.
* Add 'show images' as option in feeds.
* Support for alt attributes displayed in Feeds
* Improved URL generation to link to searched Tweets.
* Improve parsing of URLs in Tweets.
* Don't save URLs if no shortener used or shortener returns no value.
* Option to ignore stored URLs when sending Tweets.
* Code now conforms with WordPress PHP standards with the exception of four deprecated functions.

= 3.3.2 =

* If short URL already stored, do not execute shortening routine
* Remove instances of create_function for PHP 7.2 compat
* Remove language files completely in favor of WordPress.org translations
* CSS fix
* Minor text changes

= 3.3.1 =

* Add temporary method to extend character count. Twitter has not yet released their new character counting library.
* Minor style changes

= 3.3.0 =

* Bug fix: Fix arguments when using keywords with YOURLS
* Bug fix: Problem saving settings in PHP 7.1 due to array assignment changes
* New: Add filter to provide custom support any taxonomy as hashtags, 'wpt_hash_source' & 'wpt_hash_tag_sources'
* New: Add cache refresh checkbox for Tweet widget
* Update: Rewritten debugging mechanism
* New: admin notice to indicate in debugging.
* Remove Freemius (with all thanks to the Freemius team.)
* Minor tweaks to Tweet widget CSS

= 3.2.19 =

* Bug fix: account for mixed return values in get_the_tags()

= 3.2.18 =

* Bug fix: Only save last Tweet if sent successfully (See https://wordpress.org/support/topic/character-count-not-updating-and-subsequent-tweets-not-going-through/#post-9338623)
* Bug fix: in truncation settings, match displayed tag names to tags used in templates.
* Text fixes: clarify YOURLS settings notices & fields
* Add option: Hash tags from categories instead of tags
* Bug fix: incorrect url

= 3.2.17 =

* Function name change in primary function. 
* Early exit in wpt-feed
* Fix icon in metabox headings
* Misc. minor design tweaks

= 3.2.16 =

* Bug fix: missing check to verify array caused AJAX error

= 3.2.15 =

* Bug fix: "Tweet Now" button threw error if selecting main site account [Pro]
* New action executed when posting to Twitter
* New debugging point in media retrieval

= 3.2.14 =

* Bug fix: activation status of licenses in WP Tweets Pro misreported in support data
* Removed longurl.org expander since the service has been shut down.
* Exclude uploaded media URLs from character counting (WP Tweets Pro)
* Feature: Support adding custom templates for specific taxonomy terms (WP Tweets Pro)

= 3.2.13 =

* Bug fix: help/config should not be queried if user has not yet authenticated.

= 3.2.12 =

* Bug fix: call help/config to check t.co URL lengths and make sure length used is current value
* Parse URLs in text and send to URL shortener before Tweeting.
* Test for WordPress 4.6

= 3.2.11 =

* Two new filters in post meta box
* Add option to set your own Goo.gl API key for improved shortener reliability
* Removed my fallback functions for mb_substr and mb_strlen & support for WordPress 4.1
* Fixed a broken URL
* Updated sales copy

= 3.2.10 =

* Bug fix: extra closing `p` tag in widget output.
* Feature: pattern for getting arbitrary author meta: {{meta_field}}
* Minor security fix: ignored wpnonce verification if nonce not provided in settings admin.

= 3.2.9 =

* Bug fix: extra is_admin call in Freemius implementation
* Feature: 'Tweet Now' & dynamic scheduling recognizes currently selected users & upload media status (Pro)

= 3.2.8 =

* Bug fix: Stray debugging email in curl processing.

= 3.2.7 =

* Feature: prevent Duplicate Posts plug-in from copying WP to Twitter meta data
* Feature: add curl fallback in case WP_http doesn't function correctly.
* Feature: support for image alt attributes in widget
* Feature: support for selective refresh in customizer
* Feature: improved error messages from Twitter
* Change: added Freemius service back to plug-in
* Bug fix: disconnect Twitter account in user accounts (PRO)

= 3.2.6 =

* Bug fix: wrap Twitter follow button in div to prevent obscure Blink rendering bug.
* Bug fix: obscure bug saving incorrect short URL when saving draft

= 3.2.5 =

* Bug fix: added prefix to is_valid_url (function used by some other plug-ins)
* Bug fix: undismissable promotion for WP Tweets PRO
* Minor style changes

= 3.2.4 =

* Bug fix: functionalized uninstall, but placed in file only imported while WPT active.

= 3.2.3 =

* Remove Freemius integration due to excessive API load.

= 3.2.2 =

* Only call Freemius integration in admin.

= 3.2.1 =

* Bug fix: uninstall issue with Freemius
* Bug fix: extraneous function call with Freemius
* More style streamlining

= 3.2.0 =

* Bug fix: if user without permissions to edit WP to Twitter meta updated profiles, Twitter profile data was deleted.
* Bug fix: PHP notices (2) in Twitter search widget
* Bug fix: no notice to update settings when setting new URL shortener.
* Bug fix: permissions tabs non functional if custom role name had a space
* Bug fix: remove notice thrown when rate limiting is run on a Tweet not associated with a post
* Bug fix: remove notice thrown when no error defined by custom shortener.
* Design update in metabox panel
* Misc. design & text updates
* Ability to add new URL shorteners via filters ('wpt_shorten_link', 'wpt_shortener_settings', 'wpt_choose_shortener')
* Remove ability to set YOURLS as a local resource in new installs
* Added filter to disable storing URLs in post meta
* Deprecate more old jd_ prefixed functions
* Change admin page URL to match Pro version.
* Remove dependency on is_plugin_active()
* Added opt-in usage tracking via Freemius.com

= 3.1.9 =

* CSS update in Twitter feed for new iframe generated follow button
* Include target URL in information deleted when a post's Tweet History cleared
* Minor design changes
* Updated manual
* Updated text

= 3.1.8 =

* Bug fix: Add support for calendar picker in WP Tweets Pro
* New filter on random delay value

= 3.1.7 =

* Bug fix: mismatched argument count in replacements caused & to be replaced with null
* Bug fix: PHP notice on Advanced Settings screen
* Bug fix: append/prepend fields accidentally eliminated from Tweet output in truncation rewrite

= 3.1.6 =

* Rewrite: Rewrite Tweet truncation code.
* Bug fix: Make charcount aware of #longurl#
* Open up possibility of reposting more than 3 times in WP Tweets PRO through filters.
* Bug fix: issue with character counting on Scheduled Tweets screen.
* Add textdomain to plug-in header

= 3.1.5 =

* New filter allows disabling storing short URLs `wpt_store_urls`; return false.
* Disable migration routine as DB-wide function; handle only on post edit.
* Eliminate some unused variables.
* Change primary settings headings to H1 on WP 4.3 and above.
* Removed collapsible panels in settings. These are irrelevant with tabbed interface.
* Minor design changes.

= 3.1.4 =

* CSS fix for 4.3 compatibility. 
* Avoid error if administrator role is missing.
* Prevent setting rate limiting to 0.

= 3.1.3 =

* Bug fix: Fix a fallback function for mb_substr
* Bug fix: Missing Urlencoding on YOURLS post titles caused return as XML instead of JSON
* Bug fix: one rate limiting setting not deleted on uninstall
* Update Language: Australian English 

= 3.1.2 =

* Misnamed variable in 3.1.1.
* Minor update to Dutch translation
* Added partial Australian English translation

= 3.1.1 =

* Add post title to Yourls shortener query. Thanks to <a href="https://wordpress.org/support/topic/missing-post-title-on-remote-yourls-call-fix-included?replies=1">the.mnbvcx</a>.
* Bug fix: Overlooked warning if categories not defined.
* Updated wp-to-twitter.pot

= 3.1.0 = 

* Moved changelog entries older than 3.0.0 into changelog.txt
* Update PHP 4 class constructors to PHP 5.
* Added template tags for all categories and all category descriptions.
* Better loading of text domain.
* Improve preview character counting when featured images are being uploaded. (WP Tweets PRO)
* Require users to add an email to send a support request.
* Added check for constant WPT_STAGING_MODE; disables posting to Twitter on staging servers.
* New feature: Rate limiting. Enable rate limiting to restrict the number of posts per category per hour can be sent to your Twitter account.

= 3.0.7 =

* Bug fix: Twitter Feed search broken.
* Bug fix: Display issue with support form textarea.
* Address issue with input sources that have double encoded entities.
* Improved: Error messages with Twitter Feed issues.
* Add option to hide header on Twitter feed widget.
* Language Update: Portuguese (Brazil)

= 3.0.6 =

* Bug fix: missing styles from Twitter feed
* Bug fix: test whether Tweet is possibly sensitive always returned true
* New feature: display uploaded images in Twitter feed instead of link to image.
* New template tag: #longurl# - use to Tweet the unshortened URL for a post.

= 3.0.5 =

* Bug fix: Typo in fix for settings update screwed things up.

= 3.0.4 =

* Bug fix: Error with YOURLS url handler. Two reversed variable definitions.
* Bug fix: Bad URL for testing time check when WP Tweets PRO active.
* Bug fix: Update could reset some settings to defaults.
* Grammar fix to one text string. 
* Minor updates to Spanish & Portuguese translations

= 3.0.3 =

* Update Japanese translation
* Bug fix: accidentally left one debug message in override.

= 3.0.2 =

* Bug fix: obscure duplicating Tweets issue related to co-Tweeting and media uploads
* Bug fix: notice thrown if using Yourls and access to Yourls directory blocked at server.
* Revamped settings page. 
* Updated user's guide.

= 3.0.1 =

* Changed priority of wpt_twit function on save_post action so that The Events Calendar can send Tweets.
* Bug fix: ensure that arguments passed to URL shorteners for analytics are URL encoded.
* Bug fix: Clear widget cache when widget is updated.
* Bug fix: invalid argument with obsolete category filters.
* Bug fix: inconsistent labeling of API key/consumer key. 
* Bug fix: Errors in data migration for 3.0.0 fixed.
* Only show 'Tweet to' tab if individual authors options are enabled.
* Minor updates to application setup instructions.

= 3.0.0 =

* Handles case where post type identification could throw PHP warning if no post types were chosen to be Tweeted.
* Eliminated outdated compatibility function. 
* Eliminated old update notices.
* General code cleanup.
* Code documentation.
* Updated media uploading to use Uploads endpoint, replacing deprecated update_with_media endpoint. [WP Tweets PRO]
* Simplifed short URL storage
* Decreased widget cache life from 1 hour to 30 minutes.
* Added fallback Normalizer class for cases when extension is not installed.
* Added notes for the 100 HTTP code return error.
* Moved Twitter server time check out of basic set-up & set up to only run on demand.
* Minor design changes.

== Installation ==

1. Upload the `wp-to-twitter` folder to your `/wp-content/plugins/` directory
2. Activate the plugin using the `Plugins` menu in WordPress
3. Go to Settings > WP to Twitter
4. Adjust the WP to Twitter Options as you prefer them. 
5. Create a Twitter application at Twitter and Configure your OAuth keys

== Frequently Asked Questions ==

= Where are your Frequently Asked Questions? Why aren't they here? =

Right here: [WP to Twitter FAQ](http://www.joedolson.com/wp-to-twitter/support-2/). I don't maintain them here because I would prefer to only maintain one copy. This is better for everybody, since the responses are much more likely to be up to date!

= Twitter's Application creation process is very difficult. Why do I have to do this? =

WP to Twitter has always followed the principle that you are the owner of your own application. Many other applications require you to pass your data through a 3rd party that you authenticate to post to Twitter. Twitter has gradually made the process to create a new application more and more difficult. There is nothing I can do about that. 

= How can I help you make WP to Twitter a better plug-in? =

Writing and maintaining a plug-in is a lot of work. You can help me by providing detailed support requests (which saves me time), or by providing financial support, either via my [plug-in donations page](https://www.joedolson.com/donate/) or by [upgrading to WP Tweets Pro](http://www.wptweetspro.com/wp-tweets-pro). Believe me, your support really makes a difference!

== Screenshots ==

1. WP to Twitter Set up.
2. WP to Twitter Post Meta box.
3. WP to Twitter post meta box with WP Tweets PRO active.
4. WP Tweets PRO settings.
5. Example Twitter Feed (Twenty Nineteen)
6. Basic WP to Twitter Settings

== Upgrade Notice ==
