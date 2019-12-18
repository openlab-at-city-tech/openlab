# Changelog

## 1.9.2 _(2019-12-07)_
* Fix: Correct typo in GitHub URL
* Unit test:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * Change: Update expected frontend output to include additional "ugc" (user generated content) value now included for `rel="nofollow"` attribute of commenter links
* Change: Note compatibility through WP 5.3+
* Change: Update copyright date (2020)

## 1.9.1 _(2019-04-05)_
* Change: Initialize plugin on `plugins_loaded` action instead of on load
* Change: Merge `do_init()` into `init()`
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* Change: Unit tests: Specify hook priority when testing via `has_filter()`
* Change: Note compatibility through WP 5.1+
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

## 1.9 _(2017-11-06)_
* Bugfix: Explicitly set comment count font color to avoid style conflict with Akismet that resulted in gray text on dark gray background
* Bugfix: Disable Akismet's version of the functionality since it is duplicative and interferes with author section layout
* Change: Omit unnecessary `wp_register_style()` and instead provide all arguments to `wp_enqueue_style()`
* Harden: Use 'esc_like()` on the pingback/trackback URL prior to use in queries
* Bugfix: Use proper existing variable when searching for other pingbacks/trackbacks
* Change: Omit unnecessary appending of '%' to author_url value in call to `get_comments_count()`
* New: Add README.md
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Update copyright date (2018)
* Change: Minor whitespace tweaks in unit test bootstrap

## 1.8 _(2017-03-04)_
* Change: Adopt WP core style of showing pending comments in a red circle superscript to comments bubble icon
* Change: Show comments bubble in "Comments" column of user listings instead of plain integer
* Change: Don't link comments bubble when there are zero comments since approved and pending comments are available as separate links
* Change: Extract comments bubble markup generation from `comment_author()` into new `get_comments_bubble()`
* Change: Remove support for pre-WP 4.3 markup
* Change: Prevent object instantiation of the class
* Change: Use `sprintf()` to produce markup rather than concatenating various strings, function calls, and variables
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Note compatibility through WP 4.7+
* Change: Remove support for WordPress older than 4.6 (should still work for earlier versions back to WP 4.3)
* Change: Update readme.txt content and formatting
* Change: Update copyright date (2017)
* Change: Update screenshots
* New: Add LICENSE file

## 1.7 _(2016-01-11)_
* Bugfix: Fix bug preventing display of values for other custom columns in users table
* Add: Memoize commenter counts so they aren't re-queried more than once per page load
* Add: Add support for language packs by loading textdomain and explicit using it
* Add: Add inline docs for class variables
* Add: Add public method `reset_cache()`
* Add: Add private variable `$memoized`
* Change: Note compatibility through WP 4.4+
* Change: Explicitly declare methods in unit tests as public
* Change: Update copyright date (2016)
* Add: Create empty index.php to prevent files from being listed if web server has enabled directory listings

## 1.6 _(2015-09-19)_
* Bugfix: Add support for changes in WP 4.3 (to fix display of comment bubble background).
* Change: Minor inline documentation spacing tweaks.
* Add: Add 'Text Domain' field to plugin header.
* Change: Note compatibility through WP 4.3+.

## 1.5 _(2015-02-05)_
* Add `is_admin()` check to `comment_author()`
* Always load class rather than just in the admin
* Add to, and improve, unit tests
* Add screenshot showing 'Comments' column in user listing
* Curly-braced variables used in strings
* Note compatibility through WP 4.1+
* Update copyright date (2015)

## 1.4 _(2014-08-30)_
* Add 'Comments' column to admin user listing with linked count of that user's comments
* Modify markup output to accommodate changes made in WP 3.9
* Abstract comment count logic into `get_comments_count()`
* Abstract admin comments URL link into `get_comments_url()`
* Remove commented out styles
* Minor plugin header reformatting
* Add more unit tests
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Note compatibility through WP 4.0+
* Drop compatibility with version of WP older than 3.9
* Change banner image
* Add plugin icon

## 1.3 _(2013-12-23)_
* Enqueue custom CSS file instead of adding CSS to page head
* Change CSS to allow comment bubbles to take on colors of active admin theme
* Change initialization to fire on `plugins_loaded`
* Add unit tests
* Minor documentation tweaks
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.8
* Update copyright date (2014)
* Change donate link
* Update screenshots for WP 3.8 admin refresh
* Update banner for WP 3.8 admin refresh

## 1.2.1
* Add check to prevent execution of code if file is directly accessed
* Note compatibility through WP 3.5+
* Update copyright date (2013)
* Move screenshots into repo's assets directory

## 1.2
* Add CSS rule to set text color to white to supersede CSS styling done by latest Akismet
* Default to gray comment bubble
* Show blue comment bubble for authors with pending comment (consistent with how WP does it for posts)
* Add 'author-com-pending' class to link when author has pending comments
* Show orange comment bubble on hover over comment bubble
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Add banner image for plugin page
* Remove ending PHP close tag
* Note compatibility through WP 3.4+

## 1.1.4
* Bugfix for notices when non-standard comment types are present (by explicitly supporting pingbacks and trackbacks, and ignoring non-standard comment types)
* CSS tweak to prevent top of comment bubble from being clipped
* Prefix class name with 'c2c_'
* Add `version()` to return plugin version
* Note compatibility through WP 3.3+
* Fix typo in readme.txt
* Update screenshot-3
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

## 1.1.3
* Properly encode emails in links to commenter's comments listing (fixes bug where a '+' in email prevented being able to see their listing)
* Invoke class function internally via self instead of using actual classname
* Note compatibility through WP 3.2+
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

## 1.1.2
* Explicitly declare all class functions public static
* Minor code reformatting (spacing) and doc tweaks
* Note compatibility with WP 3.1+
* Update copyright date (2011)

## 1.1.1
* Bug fix (missing argument for `sprintf()` replacement)

## 1.1
* If a commenter does not have an email provided, search for other comments based on the provided name
* Treat class as a namespace rather than instantiating it as an object
* Check for `is_admin()` before defining class rather than during constructor
* Proper conditional string pluralization and localization support
* Use `esc_attr()` instead of `attribute_escape()`
* Fix dashboard display of commenter comment counts (prevent clipping of top of bubble, bubble background is now blue instead of gray)
* No longer define background-position in CSS
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Minor code reformatting (spacing)
* Add package info to top of plugin file
* Remove trailing whitespace in docs
* Add Upgrade Notice section to readme.txt
* Note compatibility with WP 3.0+
* Drop compatibility with version of WP older than 2.8

## 1.0.1
* Add PHPDoc documentation
* Note compatibility with WP 2.9+
* Update copyright date and readme.txt

## 1.0
* Initial release
