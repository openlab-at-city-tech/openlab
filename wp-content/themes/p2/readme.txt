=== P2 ===

A group blog theme for short update messages, inspired by Twitter.

== Description ==

P2 is shorter, better, faster, stronger.
http://p2theme.com/

P2 is a theme for WordPress that transforms a mild-mannered blog into a super-blog with features like inline comments on the homepage, a posting form right on the homepage, inline editing of posts and comments, real-time updates so new posts and comments come in without reloading, and much more.

P2 is available on WordPress.com: http://wordpress.com/signup/?ref=p2
...or you can download it for WordPress.org: http://wordpress.org/themes/p2

You can also check out a demo of the theme in action: http://p2demo.wordpress.com/
If you need P2 support or want to pitch in, drop a line on the forums: http://wordpress.org/tags/p2

== Further reading ==

Matt Mullenweg: How P2 changed Automattic:
http://ma.tt/2009/05/how-p2-changed-automattic/

Official announcement post on WordPress.com:
http://en.blog.wordpress.com/2009/03/11/p2-the-new-prologue/

== Changelog ==

= 29 January 2016 =
* Switch the order of esc_like/escape calls.

= 29 October 2015 =
* Properly format last entry of changelog in readme.txt

= 5 October 2015 =
* Refactor to use `$wpdb->esc_like` instead of `like_escape` (deprecated)
* Add missing text domain on translation functions

= 1 October 2015 =
* Remove PHP strict warnings regarding usage of strict functions.

= 30 September 2015 =
* Add styles for HTML5 form inputs. Closes #3441
* Remove PHP notices from frontend/backend. Closes #3443

= 29 September 2015 =
* Display "Add Media" link on sites loaded via HTTP. Closes #3103
* Properly clear floats in galleries.
* Remove indentation spaces in footer.php
* Add theme showcase link on footer. Closes #3007
* Ensure title is displayed when title text is found at the beginning of post content.
* Don't focus textarea when in Customizer context.
* Add filter to allow microformat date localization. Closes #2838

= 20 August 2015 =
* Add text domain and/or remove domain path. (O-S)

= 21 June 2015 =
* Updated sidebar init function to hook into widgets_init action, not filter.

= 19 June 2015 =
* Removed important rules on disabled class.

= 18 June 2015 =
* Added id 'sidebar-1' to register_sidebar call.

= 9 April 2015 =
* Make search widget submit button on a separate line to avoid issues with translations and remove the width restriction.

= 8 April 2015 =
* Run the text highlighting filter later to avoid shortcode conflicts.

= 23 January 2015 =
* Check if a user agent is set, before trying to access it.

= 2 October 2014 =
* P2 "Classic" update theme name.

= 24 July 2014 =
* change theme/author URIs and footer links to `wordpress.com/themes`.

= 21 July 2014 =
* allow commenting on attachment posts, and fix doctype to avoid layout bugs.

= 18 June 2014 =
* add `static` keyword to methods to avoid `E_STRICT` level warnings in PHP.

= 17 June 2014 =
* fix a bug where Author users could not add media via front end editor. Props mfkelly.
* fix PHP warning where functions could not be called statically in strict mode.
* fix menu display bug when custom header image is enabled and header text is hidden + a menu is visible.
* fix audio player styles. Props targz-1, closes #2392.

= 12 June 2014 =
* move viewport meta element to header, to load for all user agents. Props ryansommers.
* allow user scaling on mobile version, props ryansommers.

= 5 June 2014 =
* update readme.txt to give proper credit to bug reporter.

= 4 June 2014 =
* fix tooltip issue with Recent Comments widget, closes #2466. Props mdawaffe.

= 1 June 2014 =
* add/update pot files.

= 26 February 2014 =
* Change text strings to reduce theme string proliferation.
* Change text strings to reduce theme string proliferation.

= 24 February 2014 =
* Add display inline-block to sharedaddy lists to avoid issue with lists in the content.

= 13 February 2014 =
* update screenshot to 880x660.

= 12 February 2014 =
* add earlier filter to mentions URLs in case term doesn't exist, props nacin.

= 10 January 2014 =
* remove reliance on `is_super_admin()` for mention functionality.

= 3 January 2014 =
* add ID argument when applying `the_title` filters, to match core.

= 6 December 2013 =
* update Width terms to Layout.

= 4 December 2013 =
* Add !important to #wrapper width property to fix iPhone stylesheet issue if no sidebar option is ticked.

= 8 November 2013 =
* update description.

= 1 November 2013 =
* trigger custom JavaScript event when new post is created or edited, props iandunn.

= 30 October 2013 =
* Swap out get_term_link for get_tag_link to avoid fatal errors when an error object is returned.
* better not found message on author results.
* fix broken "selected" class values for post form.

= 15 October 2013 =
* Include a random query argument in the "Am I logged in?" HTTP request.

= 9 October 2013 =
* correct language files for Turkish
* add Turkish language files

= 18 September 2013 =
* Fix keyboard keys and keyboard shortcut menu clash.

= 19 August 2013 =
* minor JS fixes to add missing semicolons and better check for updating title with `newupdates` count. Closes #1878.
* add Serbian translation, props Andrijana Nikolic

= 16 August 2013 =
* when hide comments on homepage option is on, don't try to link to in-page comments in Recent Comments widget.

= 7 August 2013 =
* only implement "p2_hide_threads" theme option when on non-singular views.

= 6 August 2013 =
* don't try to localize post dates for previews as the `post_date_gmt` it relies on is not set yet.

= 5 August 2013 =
* update author in footer and stylesheet.

= 23 July 2013 =
* allow the "Posting Access" theme option to work correctly. Was broken for a bit after refactoring.

= 16 July 2013 =
* remove /extend from WP.org directory URLs.

= 2 July 2013 =
* Fix display of empty comments. This can happen on edit. Props nacin.

= 1 July 2013 =
* Move away from using deprecated functions and improve compliance with .org theme review guidelines.

= 7 June 2013 =
* Updated pot file.
* Avoid strict warnings on non-static methods that are called as static.
* Removed premature 3.6 compat.
* Prep for submitting v1.5 to extend.
* p2
* p2

= 13 May 2013 =
* Making minor adjustments to print stylesheet to fix formatting issues, and to increase the main font size slightly.
* Update license.

= 7 May 2013 =
* `current_user_can( 'read' )` is better than `is_user_member_of_blog()` afterall.
* Logged in non-members need to be able to access the logged_in_out action as well.

= 4 May 2013 =
* Call P2's upgrade routine before dispatching AJAX requests.

= 3 May 2013 =
* Typo: stray comma.
* P2 can upgrade itself from blog-side via an AJAX call if it thinks it needs it.
* Split AJAX calls into two groups.
* Add forward compat with 3.6.

= 1 May 2013 =
* Add forward compat with 3.6.
* Make error message translatable.
* Avoid a warning when the comment is null.
* add Swedish (sv_SE) language files, props tdh (Thord Daniel Hedengren)

= 11 April 2013 =
* Fix a bunch of JS errors caused by the x-post autocomplete menu

= 5 April 2013 =
* revert [13512]
* Split AJAX calls into two groups.

= 27 March 2013 =
* Remove privacy check which is not .org-compatible.
* On Private blogs, don't respond to requests for recent posts/comments unless the user is known.

= 28 February 2013 =
* send_to_editor cannot be a private function.

= 26 February 2013 =
* Enqueue scripts and styles via callback.

= 25 February 2013 =
* new screenshot at 600x450 for HiDPI support.
* Restructured JS code in p2.js -

= 22 February 2013 =
* Use a filter to modify the output of wp_title().

= 21 February 2013 =
* Remove unnecessary top margin for the blog title from the iPhone style.

= 19 February 2013 =
* Quick cleanup of some formatting (whitespace) and strings (for i18n).

= 30 January 2013 =
* fix label value, props Mike Hansen

= 18 January 2013 =
* Fix typo: /s/higlighter/highlighter
* Allow comments to be temporarily highlighted when comment ID is present in the URI.

= 27 December 2012 =
* Improvements to the front-end post form.

= 12 December 2012 =
* remove style.css tag to match Theme Showcase

= 6 December 2012 =
* Remove infinite loop created by p2_fix_empty_titles().

= 30 November 2012 =
* Allow the comment form to appear below a newly published post loaded vai Ajax.

= 28 November 2012 =
* p2 compat with new media
* Restore slideUp() animation in the comment form.

= 22 November 2012 =
* Enable comments to be posted via iDevice.

= 6 November 2012 =
* Send no-cache headers and 200 response when no results are returned by P2Ajax::get_latest_posts() and P2Ajax::get_latest_comments().
* Use is_object_in_taxonomy to check whether to display tags or not.

= 2 November 2012 =
* Allow tabbing from content box to tabs input on post form.

= 17 October 2012 =
* Only output the permalink HTML once. Conditionally add the "printer-only" class for the singular view.

= 9 October 2012 =
* exclude iPad from mobile version, styles are intended for iPhone only. Props danroundhill.

= 2 October 2012 =
* 100% table width looks bad in tables with large data

= 30 September 2012 =
* Allow the permalink to appear on single posts on the print-view only.
* Increase right, top, and bottom margins on the print stylesheet to match the left margin. Add underline to tags, and increase text size.

= 28 September 2012 =
* Display permalink above tags on the print stylesheet, with full url for permalink. Display shortlink on single posts.
* Allow tables to take full width on the print stylesheet.
* Tweak P2 print styles to remove errant bullets, widen right margin, decrease font size slightly and increase size of post titles.

= 27 September 2012 =
* Add collaboration to style.css tags to match the tags that are on the Showcase Page for P2.

= 12 September 2012 =
* Restore loading of style-iphone.css, remove style-responsive.css

= 11 September 2012 =
* Further responsive styles for P2.
* Add function_exists() call to is_automattician() so we don't break dot org sites using P2 from the themes repo.

= 10 September 2012 =
* Begin initial responsive styling. Simplifying existing styles, hiding unneeded elements, for P2.
* Don't show the iPhone mobile styles when not on iPhone. :)
* Add style-responsive.css to P2, and load this file instead of style-iphone.css for members of Team Cordova to test.

= 22 August 2012 =
* allow autogrow textareas on any view (not just front page). Props westi.

= 8 August 2012 =
* localizeMicroformatDates() assumes that elements have title attributes. We should check for the existence of titles before passing their values to parseISO8601().

= 7 August 2012 =
* s/wp_enqueue_styles/wp_enqueue_scripts
* First pass at a custom print stylesheet.

= 1 August 2012 =
* More robust list creator logic.

= 26 July 2012 =
* move header link color into stylesheet, will be overridden by 1) core header text color or 2) plugins.

= 25 July 2012 =
* Make spinner size and color match Instapost.
* Add custom colors tag to style.css
* Style tables in comments like they are styled in post content.

= 20 July 2012 =
* Removed custom colors tag from style.css until background images from theme options are working properly
* Allow tags to be edited from the front-end.
* Added custom colors tag to style.css

= 14 July 2012 =
* P2 Fix margin bug for the post post-format in Chrome nightlies.

= 13 July 2012 =
* 1.4.2 Bump
* shrink the post box's textarea a bit to fix display bug in Google chrome nightlies. props iamtakashi cainm
* Update screenshot to reflect changes new/removed icons.

= 12 July 2012 =
* remove duplicate tag.
* More specific selector for retina media upload button.
* Retina all of the things!

= 6 July 2012 =
* Background images should display at actualy size on retina screens.
* Use a Retina-friendly spinner.

= 15 June 2012 =
* remove svn:executable property.

= 23 May 2012 =
* Revert unintended changes from r9585
* Move into pub/ and temporarily add to .ignore.

= 21 May 2012 =
* Improve default styling for tables. Added 100% width and padding to entire table, and a background color and bold text to th.

= 16 May 2012 =
* Make the text of each to-do item clickable. Props josephscott -

= 1 May 2012 =
* better fix for nested lists in children comments.
* fix left margin for list items in nested children commments.

= 25 April 2012 =
* The like feature now uses classes instead of IDs

= 18 April 2012 =
* Update a typo in the theme tags in style.css so that the styles in the css file match those in the showcase.
* enable print styles.

= 7 April 2012 =
* Update pot.
* Version Bump & update feature tags in style.css
* Update readme.txt to summarize 1.4 changes.
* Remove post_flair_hooks filter from quotes.
* Backward compatibility for custom header and background in versions of WordPress < 3.4.
* Escape internationalized text printed in html attributes.

= 6 April 2012 =
* Remove commented code in stylesheet.
* Remove commented code.
* Fix invalid markup for AJAX comment form on homepage. props danielbachhuber.

= 3 April 2012 =
* iPhone style
* CSS coding standards for iPhone and custom header.
* Add support for custom menus.

= 31 March 2012 =
* Custom header text in admin ui should match that in template.
* Update DocBlocks.
* Organize functions file:

= 16 March 2012 =
* Ensure that mentions get recorded in post/comment meta.

= 10 March 2012 =
* Variables should not be passed to gettext functions.

= 27 February 2012 =
* Remove Instapost.

= 21 February 2012 =
* Avoid fatal errors where get_term_link() returns a WP_Error object. props westi
* Allow mentions to work with default permalink setting.
* Remove call to get_users_of_blogs() to pass Theme Check scan.
* Add textdomain for self-hosted users.
* wrapper background in the rtl stylesheet should match that in style.css.
* Textarea Improvements
* Add post flair for 'quote' formatted posts.

= 17 February 2012 =
* Enable compatibility with WP v3.1.
* When a user manually resizes the post or respond box in, say Chrome, make sure that the resize happens vertically only, not horizontally into the sidebar area.

= 15 February 2012 =
* Add 10 pixels to Milestone widget's width.
* Basic styling for the Milestone widget.
* Use dollar sign to reference the jQuery object. options-page.php has been skipped as the js will be removed soon.
* Better i18n for strings in p2_get_discussion_links().

= 8 February 2012 =
* Add styling for abbr element. props @philiparthurmoore.

= 27 January 2012 =
* remove business tagging from subjects list

= 19 January 2012 =
* remove extraneous css

= 18 January 2012 =
* latex images should not be forced to display block

= 2 January 2012 =
* Replace call to deprecated function  get_userdatabylogin() with non-deprecated function.

= 23 December 2011 =
* Replace calls to deprecated function get_userdatabylogin().

= 16 December 2011 =
* update fix in r8392 to use correct selector.
* Piano Black, Liquorice, P2, The Morning After, Neutra, Clean Home: add generic action-hooks to header and sidebar.

= 15 December 2011 =
* clean up extra tabs
* styles for the Link Post Format;

= 14 December 2011 =
* remove debug and unused code from r8446.P2: remove debug and unused code from r8446.)
* add support for post formats.

= 12 December 2011 =
* Front-end edit form should ignore sourcecode shortcode. props Viper007Bond.

= 8 December 2011 =
* override login styles for Like form so that the logged-out popup is usable.

= 6 December 2011 =
* P2

= 18 November 2011 =
* Update the core P2 theme to allow instapost to override the default posting interface. If Instapost code cannot be found then the theme will fall back to the original post form.

= 10 November 2011 =
* P2
* Hook into the post_comments_link filter so the reply link also outputs a title attribute (like the other action links)
* Add title attributes to the P2 action links (permalink, edit, etc) so that there are tooltips on link hover.

= 9 November 2011 =
* adding a class to the comment permalink to match the post permalink so it's easier to style

= 7 November 2011 =
* P2

= 3 November 2011 =
* Updated FR translation from ms-studio
* P2

= 2 November 2011 =
* P2

= 1 November 2011 =
* P2
* highlight current user's mentions a bit more than the rest.
* Allow for mention highlighting to be more targetted.

= 25 October 2011 =
* Team Santa
* Team Santa
* Team Santa
* Team Santa
* Team Santa
* Santa fix
* team Santa adds autocomplete improvements.
* Team Santa
* Team Santa
* Team Santa Stealth Commit

= 14 October 2011 =
* show the extra links in the iPhone view.
* add an extra set of action links to above the post, since Reply is the only action used in the main post area.

= 7 October 2011 =
* revert r7642 as it causes fatal errors

= 6 October 2011 =
* updated pt_PT translation from vanillalounge
* More P2 style
* More P2 style
* More P2 style
* More P2 style

= 5 October 2011 =
* More P2 style
* More P2 style
* More P2 style
* More P2 style
* More P2 style fixes for iPhone. (Stupid iPhone simulator is not the same as an actual device)
* More P2 style
* More P2 style
* More P2 style
* More P2 style
* Make sure the post button only shows if the user can the permission to post on a P2.
* More P2 style
* Fix up the iPhone styles for P2 theme so it's actually usable and readable.

= 4 October 2011 =
* improve mentions.php for backcompat with older versions of WP (< 3.1).

= 3 October 2011 =
* P2 should allow you to toggle comment threads on tag archives, props westi.

= 20 September 2011 =
* escape translatable attribute values with esc_attr_e().

= 12 September 2011 =
* escape get_comment_link() properly
* updated Italian translation, add Kurdish translation

= 9 September 2011 =
* set svn:eol-style on JS and TXT files

= 19 August 2011 =
* P2 JS bump for r7134
* When you have the tag list open, hitting Return shouldn't submit the post, it should do nothing. Props andrewspittle for the bug report.

= 7 August 2011 =
* before adding links to mention names, make sure the name only appears in the list once. This

= 3 August 2011 =
* hide the actions bar when using inline editing tool for post or page.

= 2 August 2011 =
* restore missing echo command, lost in r5968.

= 20 July 2011 =
* P2, wptouch, Liquorice: Use integer for $content_width value, not string.

= 12 July 2011 =
* update POT, readme, and version for 1.3.1 release
* move new messages to wp_localize_script so they can be translated. Props westi for the reminder.
* better placement for new posts under sticky post(s). Closes #898.

= 7 July 2011 =
* This P2 ride is bump-y.
* place new posts after sticky posts.

= 5 July 2011 =
* better handling of logged out and offline checks to avoid delays in normal activity if logged in.
* use user_nicename for @-name mention hint, since that is what mentions uses.
* add author template so we can catch 404 request to non-existent authors on this blog.

= 4 July 2011 =
* make sure the special check for logged-out and offline only runs for users P2 thinks are already logged in. See #665.

= 1 July 2011 =
* minor CSS fixes for post icon sprite, footer and post box edge alignment, and spacing between posts consistency cross-browser. Props Pterodactyl.
* r6540, add comment block and make the fade delay a bit longer.
* better logged out error behavior.
* do not confuse logged out with offline.

= 30 June 2011 =
* reset the height of the new post textearea after a successful post.

= 28 June 2011 =
* change sticky color to blue.

= 22 June 2011 =
* updated Uighur language files, readme updates for current release

= 21 June 2011 =
* JS fixes for jQuery compatibility.

= 17 June 2011 =
* $themecolors updates.

= 8 June 2011 =
* Add p2_found_mentions filter to allow plugins to alter which mentions are attached to a post/comment.

= 7 June 2011 =
* Enable @-completion for super admins.
* Change 'cancel reply' hotkey to shift+esc and add an 'are you sure' dialog.

= 10 May 2011 =
* Autocomplete: do not automatically select the first attribute.

= 3 May 2011 =
* clear the post title and quote citation after a new post
* add starting point for posttext textarea to avoid vertical jump after focus

= 26 April 2011 =
* Take domain mapping into account when generating the ajax url.
* Take use_ssl user option into account when deciding to display media buttons.

= 19 April 2011 =
* validate custom background input for proper format; add missing # if not in color value -
* fix Recent Comments widget to clear cache on comment delete -

= 15 April 2011 =
* "09 is not a legal ECMA-262 octal constant"

= 14 April 2011 =
* Separate JS locale formatting into its own PHP file.

= 11 April 2011 =
* make sure the_author does not incur in extra spaces so that the underlining is kept to the words

= 9 April 2011 =
* Fix malformed dates in Chrome. Ensure JS locale array keys are accurate.
* fix comment permalinks (missing echo) -

= 8 April 2011 =
* Refactor P2 into components, improve mentions and autocomplete, and many minor improvements;

= 8 March 2011 =
* remove WP.com note from changelog; CSS cleanup

= 28 February 2011 =
* add missing textdomain
* Updated JA translation from OKAMOTO Wataru

= 23 February 2011 =
* updates for 1.2.3 release

= 22 February 2011 =
* Allow hooking into P2 to add new action links.

= 21 February 2011 =
* Run make_clickable later to avoid shortcode conflicts -

= 17 February 2011 =
* adjust styles for Authors widget so if "show all authors" widget option is checked non-linked usernames are displayed correctly.

= 14 February 2011 =
* better split for generated titles to account for long URLs -
* revert r5628 for make_clickable change

= 11 February 2011 =
* cleanup for p2_excerpted_title.
* add p2_excerpted_title to provide titles with only whole words -
* avoid make_clickable when content has pre, code, or sourcecode

= 9 February 2011 =
* Take out tag from translatable string
* allow image upload from front-end regardless of domain and HTTPS setting -
* add SK translation by angeloverona

= 7 February 2011 =
* supporting p2 code so we can hook in and add items to the post form

= 21 January 2011 =
* change include to include_once to avoid conflicts with plugins; clean up require and include calls

= 18 January 2011 =
* add DE translation props Joachim Haydecker
* fix page nav float clearing and pingback spacing; underscore to dash (another one bites the dust!)
* Escape translated strings, injected directly in JavaScript

= 13 January 2011 =
* add sticky styles. Props designsimply and westi.

= 12 January 2011 =
* two blockquotes together

= 8 January 2011 =
* a few minor rtl css updates
* i18n for p2 user completion js

= 6 January 2011 =
* initialize arrays to avoid errors

= 5 January 2011 =
* backwards compatible and single install p2_user_suggestion -
* hide screen-reader-text elements
* change discussion author links to use get_comment_author_link -
* adjust search styles to only change main content -
* hide mentions taxonomy from nav menus - props westi and ranh
* svn:mime-type application/octet-stream -
* eol-style native for PO files and a few PHP files that were missed before
* add real NL translation files and not Trac attachment pages, props westi -
* add NL translation by Remkus de Vries - closes #661

= 4 January 2011 =
* updating readme
* check for get_avatar_url before user suggestion is enabled
* update incorrect dates and changes in the readme
* make sure we're running a multisite install before we use multisite-specific functions

= 22 December 2010 =
* after inline editing on pages the title replacement was replacing all h2 elements in the page. This

= 21 December 2010 =
* remove formatting and make sure it matches the theme name in style.css -
* Make WP_User_Query return the corect format. Related to r17013-core.

= 18 December 2010 =
* add translation-ready tags for accuracy

= 16 December 2010 =
* fix comment toggle on pages

= 14 December 2010 =
* Updated tags for Monochrome to Sapphire

= 10 December 2010 =
* add a priority so the function can be easily removed/replaced
* enter now submits on autocomplete mode, uped delay and allow spaces in dropdown mode
* fix p2 at name highlighting logic. This should only run when looking at a mentions taxonomy view

= 8 December 2010 =
* add title attribute to show @name mention usernames - closes #549
* Orders tags by popularity and show counts. Have username suggest and tag autocomplete use same JS. Add username suggest and new tag counts for all P2s

= 7 December 2010 =
* revert dropdown change, filter will go here
* linebreak for tags
* remove count
* order tags by popularity for p2s and display count for tag dropdown

= 2 December 2010 =
* revert isRtl var now that it is fixed in core with #WP15639 and [WP16687]

= 1 December 2010 =
* remove debug
* P2 prep for 1.1.9 release and move changelog.txt to readme.txt
* add isRtl variable and misc cleanup; add P2_INC_URL back in
* replace get_bloginfo and bloginfo with updated function calls
* replace deprecated is_site_admin function -

= 30 November 2010 =
* improve attachment template to check for image type; for non-images show link to file
* show moderation message for comments, and make sure logged-out users
* list of commenters should not show users with unapproved comments -

= 29 November 2010 =
* change 11 to 9 so that only an integer value for this.getMonth() lower than 9 (meaning a month before October) will get the 0 prefix

= 20 November 2010 =
* fix typo that was making front-end post form miss status post type - props lloydbudd (Also make status default if type is empty)

= 17 November 2010 =
* remove JSON library (use WP built-in functions instead)
* Updated pt_PT translation files - props vanillalounge
* fix issue where adding a page to Custom Menu in wp-admin made the title be the first part of the content. Also fixes adding categories to Custom Menu (was broken).

= 16 November 2010 =
* Revert r4872 to run pre_comment_content on all requests (admin and normal)

= 12 November 2010 =
* add comment paging
* prep for 1.1.8 release

= 5 November 2010 =
* use a regex for mention replacements so parts of urls and links are not matched. Props kokejb

= 4 November 2010 =
* adjust r4870 to only filter comments for non-admin views -

= 1 November 2010 =
* $list_creator does not need to be a global. Convert lists in comments too

= 28 October 2010 =
* show Toggle Comment Threads link on search results view (and minor cleanup)

= 27 October 2010 =
* ignore inline dashes and better HTML output - props justin. Closes #571
* enable auto-parse function for unordered lists in posts - props justin (see #571)

= 21 October 2010 =
* display correct list item bullets in comments

= 19 October 2010 =
* update Spanish translation and fix two untranslated strings

= 14 October 2010 =
* fix annoying bug where unordered lists nested in ordered lists get numerical bullets

= 8 October 2010 =
* remove IS_WPCOM check for footer credit

= 7 October 2010 =
* remove permalink from page and title logic updates:
* fix several inline editing issues

= 30 September 2010 =
* prepare for 1.1.7 release
* new POT for 1.1.6.3
* add language files for Chinese, Slovenia, and Uighur

= 29 September 2010 =
* make quote content clickable in case the cite element is a link

= 28 September 2010 =
* load utils first; show autosuggest for tag editing inline (see #184 and r4682)
* cleanup up theme options code and update JS/CSS loading for front-end media functions - see r4683; bump version and update changelog for 1.1.6.3
* only load farbtastic and colorpicker on the p2 options page, instead of everywhere on the blog and wp-admin

= 27 September 2010 =
* Update P2 inline editing to support editing tags, post title, and quote fields (closes #184, refs #374)

= 24 September 2010 =
* add padding to allow 2 digit ordered lists
* typography
* fix r4647 for h4 elements in postcontent; align h1-h6 elements with better spacing, typography, and sizing

= 21 September 2010 =
* Prevent h4 from overlapping Gravatars, which reduces the hover-target for upcoming Hovercards.
* display_name instead of nickname and escape title attribute -

= 20 September 2010 =
* fix issue where posts appear repeatedly on the front page -

= 17 September 2010 =
* move submit comment progress image to right - props photomatt

= 10 September 2010 =
* A filter to make it easier to modify P2s special function.

= 30 August 2010 =
* minor adjustments to prepare for submitting again to WP.org theme review

= 26 August 2010 =
* fix overflow on postcontent lists - props photomatt

= 25 August 2010 =
* Make textarea resizing work correctly even when there are wrapped lines.
* fix comment form logged-in-as position on multiple post view

= 24 August 2010 =
* revert background transparency in r4487; better display for comment form text and submit button
* more subtle border color for author comment
* updates to pass theme review for 1.1.6

= 12 August 2010 =
* new POT for 1.1.6

= 10 August 2010 =
* fix empty title delimiter --
* revert r4376 for p2_the_title return parameter

= 9 August 2010 =
* call comment_class correctly
* change back to $echocomments
* fix empty delimiter in p2_the_title -- fixes 462; cleanup and bump version;

= 6 August 2010 =
* underscore to hyphen; add styles for network signup form -

= 4 August 2010 =
* remove unused sidebars -

= 29 July 2010 =
* allow default WP Recent Comments widget to display all avatar sizes (was hard-coded to 32px)

= 26 July 2010 =
* use post title for single post navigation -
* fix like box alignment

= 21 July 2010 =
* Improve 'autogrow' automatic resizing behavior for textareas. With this change
* fix up style.css comment headers for consistency
* allow newComment trigger to run for pages -
* fix another undefined index -
* fix another undefined index -

= 20 July 2010 =
* update changelog
* fix deprecated calls and minor PHP errors -

= 14 July 2010 =
* Fix issue with comment form on IE (ticket 369). IE doesn't support event bubbling for submit events, so live() didn't work
* Fix ajax link when using FORCE_SSL_ADMIN

= 6 July 2010 =
* new POT as of r4194
* translation
* adding Norwegian translation

= 24 June 2010 =
* code and pre size down a notch for readability

= 15 June 2010 =
* Use the standard generator and designer links for Neat, Neo-Sapien, Neutra, Ocadia, Ocean Mist, P2, Pool, Pressrow, Prologue -

= 10 June 2010 =
* p2 rm exe bits

= 8 June 2010 =
* replace deprecated wp_specialchars with esc_html; fix incorrect has_cap in theme options page call, fix i18n for context

= 4 June 2010 =
* cleanup and changelog for 1.1.5 release

= 3 June 2010 =
* Author Grid widget style updates for Bueno, P2

= 28 May 2010 =
* newComment code cleanup -

= 27 May 2010 =
* p2_title calls don't need default values
* strip out placeholder Post Title title content; remove unneeded default content set via JS --
* check null value for new posts and new comments to avoid JS null object error

= 24 May 2010 =
* Add translator credit in P2 changelog
* Add Czech translation of P2

= 14 May 2010 =
* IM in UR screenshots crushing UR PNGs

= 11 May 2010 =
* adding back singular reply box open by default

= 6 May 2010 =
* support for new Author Grid, and fix display of normal Author widget -
* changelog updated
* remove header height restriction except for header images -

= 5 May 2010 =
* fixing #274 for P2

= 28 April 2010 =
* make media URLs work again (broken in r3427)

= 26 April 2010 =
* remove top padding for nested ordered list items

= 23 April 2010 =
* medai urls are already absolute in WP 3.0
* Standardize all admin menu labels to show as "Theme Options" in Appearance menu -

= 20 April 2010 =
* replace deprecated function calls relating to escaping; use get_search_query and the_search_query -
* more attribute escaping and replace deprecated data validation function calls -

= 15 April 2010 =
* changelog update
* adding belorussian translation for p2

= 9 April 2010 =
* rename edit-post-link to avoid conflict with wpcombar class names

= 8 April 2010 =
* fix Edit Page link in adminbar - closes #282

= 29 March 2010 =
* fixing header problems with full size header link + custom header
* fixing headers that don't link when using a custom header image.

= 26 March 2010 =
* fixing possibly related styling - from email that @matt sent

= 16 March 2010 =
* Add -wpcom version suffix to theme versions (see r454)

= 4 March 2010 =
* POT, generated from r3184 (props yoav)
* P2 typos fixed

= 24 February 2010 =
* adding PT translations
* fixing js for IE

= 29 January 2010 =
* allow replies on public blogs

= 28 January 2010 =
* make comment form visible by default on pages

= 27 January 2010 =
* style and js optimizations to pave way for a delete button finally.

= 22 January 2010 =
* do not show tags on pages

= 14 January 2010 =
* Fix list styles
* Fix list styles
* No margins or padding for block elements in tables
* Remove problematic top padding from list items; remove border spacing from tables

= 12 January 2010 =
* adding sidebars, not initializing them, fixing table padding, adding french translations and updating changelog

= 24 December 2009 =
* showing respond form by default on single pages

= 22 December 2009 =
* fixing input post box padding
* fixing input post box padding
* fixing input post box padding

= 21 December 2009 =
* p2, fix subscriptions
* send subscriptions options via ajax

= 12 December 2009 =
* fixing Edit Page|Post buttons for admin bar

= 10 December 2009 =
* fixing potential cookie security problem in p2 by attaching to wp_ajax_ action so that can_user() functions actually check for proper cookies

= 8 December 2009 =
* fixing mapped domains and https/http security issue
* fixing mapped domains and https/http security issue
* fixing https/http inline edit post error

= 2 December 2009 =
* missing i18n
* update rtl sidebar back
* url should be from the url not the site url, most presumably
* updates to 1.1.2, media uploads via subdirectory
* fixing PHP4 incompatability
* fixing floats on images and a clearing problem

= 26 November 2009 =
* version bump
* fixing functions if not available.
* wp_link_pages
* final wporg
* fixing post form
* updates to changelog
* fixing author archive pages
* fixes rewrite rule flushing for p2 @mentions
* fixing page layout and edit buttons

= 25 November 2009 =
* comment progress indicator removal on success
* progress indicator position for comments
* expanding comment form

= 24 November 2009 =
* Style wpstats smiley

= 23 November 2009 =
* Add styles for lists in comments
* Making lists line up correctly, override padding from layout styles
* Remove unnecessary stuff from reset, style cites
* Make sure that array_keys gets an array to work with

= 22 November 2009 =
* action links again - better handling of permalinked pages
* p2 action links, 3rd try. Hope I got it right this time.
* move hook outside of condition so that not logged in users can

= 19 November 2009 =
* use get_permalink instead of guid
* p2, better than [2679] also handles permalinks page
* small fix to action links
* fix notifications and help on p2 rtl
* fixing overflow on code and pre tags
* make no sidebar work for RTL blogs
* p2 - new rtl.css
* p2 rtl updates
* theme name back to P2
* upgrading to p2 1.1 cross fingers

= 23 October 2009 =
* Giving P2 titles some line height so two-line titles aren't squashed

= 20 October 2009 =
* iphone postbox styles
* adding iphone styles again
* don't show author or dates on pages
* Commenting out the broken iPhone styles while they're fixed

= 14 October 2009 =
* cache bust p2 rtl.css
* updated to p2 rtl.css

= 11 October 2009 =
* get_search_query() is not escaped
* image template adjustment

= 9 October 2009 =
* Add p2_ajax hook.
* fixing issues with IE7

= 8 October 2009 =
* fixing up some header and pre styles
* adding page styles
* making css more consistent
* fixing css bugs

= 7 October 2009 =
* css
* fixing a width bug on .postcontent
* update rtl.css for p2

= 6 October 2009 =
* p2 rtl updates
* finally fixing that blasted comment logout bug - i hope.
* fixing the loggedin but forcing logout to comment bug

= 5 October 2009 =
* fixing that darn bug where comments tell you that you will lose data, even though you won't

= 3 October 2009 =
* fixing bug where comments can be lost if you are logged out as well as fixing the bug where browser may falsely alert you that you will lose a comment
* fixing bug where comments can be lost if you are logged out as well as fixing the bug where browser may falsely alert you that you will lose a comment
* reverting fix for lost comment/logout issues - going to have to find another way around it

= 2 October 2009 =
* cleaning up ajax.php
* separating templates from functions and fixing comment logout bug
* fixing comments being lost by auto-logout

= 1 October 2009 =
* Remove debug
* fixing date bug gmt
* hopefully removing all date display bugs
* fixing iphone css
* adding l and h keys
* fix login url for non .com users
* removing is_site_admin
* removing is_site_admin

= 30 September 2009 =
* make the test echo an html comment

= 29 September 2009 =
* hiding media buttons for iPhone

= 22 September 2009 =
* p2 option to allow any wordpress.com member to post

= 20 September 2009 =
* Unbreak P2 for WP.org

= 15 September 2009 =
* make it translatable
* make display titles option work for the home page

= 11 September 2009 =
* adding sticky styling
* fixing style issue on pages

= 10 September 2009 =
* css tweak for media buttons

= 7 September 2009 =
* All upload-related iframes should be loaded from the primary domain.

= 4 September 2009 =
* letting admins know the upload doesn't work

= 2 September 2009 =
* fixing issue #259

= 31 August 2009 =
* fixing single page titles and adding the option for custom prompt

= 30 August 2009 =
* commenting out some is_site_admin code that was disallowing uploads

= 28 August 2009 =
* revert r2311 which is stripping HTML and other content on single post views
* only for site admins. Causes problems with the uploader
* fixing the display of titles on single pages

= 26 August 2009 =
* updating filters for urls in p2
* media_buttons just for admins

= 18 August 2009 =
* fixing post titles

= 14 August 2009 =
* adding comments to page.php and a comment for @names code
* remove p2 debug bug from [2111] which broke everything after the first comment event

= 11 August 2009 =
* trying to solve the p2 cache bug by expiring the json data on the spot

= 4 August 2009 =
* reverting fix for line 96 until I figure out the actual cause of the problem
* filtering title through the_title filters
* fixing an error by ommitting a ! on my last commit
* fixing aligncenter styles
* checking to make sure var is array - Nikolay you may need to double check this for me.
* removing post form from everywhere but homepage

= 21 July 2009 =
* removed enqueu of stylesheet from admin pages

= 10 July 2009 =
* adding timeout for disabled form

= 7 July 2009 =
* Change TEMPLATEPATH to get_template_directory so that it works with post by email. Also shrink content_width as it was causing scrollbars on full size images

= 6 July 2009 =
* clear the right cache key on comment post

= 2 July 2009 =
* uncomment hooks to test [tasks][/tasks] shortcode
* removing hooks from, but leaving file intact
* fix faux-tasks
* revert faux-tasks
* adding faux tasks to the functions file for people to test - eventually would like @done checkboxes next to tasks with auto ajax editing
* adding in my tasks shortcode

= 1 July 2009 =
* New style widgets.

= 29 June 2009 =
* fixing a p2 style issue

= 24 June 2009 =
* use rtl-language-support as a theme tag instead
* more theme tag updates
* all of our themes have threaded comments
* page 3 theme tag updates
* use - instead of space in theme tags

= 23 June 2009 =
* Allow not logged in users to get post and comment updates
* Use sequence instead of month/date name for keys

= 22 June 2009 =
* Use the right term in search query
* Move ajax from admin-ajax to inside P2 -- the P2Ajax class
* Move options page code to inc/options-page.php
* Started dividing the functionality into more files. A huge functions.php makes me feel lost all the time.
* Compatiblity and notices.
* Comments, formatiing

= 19 June 2009 =
* don't italicize blockquotes

= 18 June 2009 =
* Use fixed version, instead of filemtime()

= 17 June 2009 =
* fixing site_url for comment form
* echo siteurl
* switching to site_url()
* hopefully fixing some commenting issues
* adding back in nikolays time function
* hopefully fixing comment issues @matt reported
* Show dates and times in local time, instead of in the blog time.
* fixing no ajax function in p2
* busting cache and updating logout code - still not perfect, but better than before
* busting the cache
* fixing some js
* temporarily fixing quotes on comments
* temporarily fixing quotes on comments
* temporarily fixing quotes on comments
* trying to fix a non-stripped slashes problem
* fixing auto suggest
* removing redundant filtering and workaround for unfiltered comments
* inverting the function, because the logic was confusing
* fix suggest support
* fixing quick edit and No Title for posts

= 15 June 2009 =
* fixing logged out and then trying to post issue, this may cause too many http calls - barry yell at me if you

= 9 June 2009 =
* fixing a __FILE__ bug
* fixing margins on reply button and changing text to read Reply instead of Post Comment

= 4 June 2009 =
* Fix double-replacement edge cases (when one user's name is a prefix of another user's name).
* prophylactic esc_attr()

= 3 June 2009 =
* move numbers inside so they are not cut off
* fixing caching for at_names function
* fixing at_names to handle both display and handle names - example @Noel Jackson and @noel
* adding support for wide videos and making at_names function super fast

= 2 June 2009 =
* fixing up focus on comment permalinks
* adding caching for user lists
* adding caching for user lists
* first run at @name functionality

= 29 May 2009 =
* use get_avatar so pingbacks show the blavatar
* pingbacks only show on single pages
* php 4 compat for str_split
* fixing get_avatar function on post-form and fixing blank titles for posts

= 27 May 2009 =
* Bust p2.js cache
* Add Fluid badge count support for P2

= 15 May 2009 =
* Bust cache
* Function name has been changed
* cleaning up index.php
* archive.php is no longer needed
* inline-comments.php is no longer needed
* adding translations and modifying changelog
* big p2 patch, most in changelog, more coming

= 14 May 2009 =
* switch to get_avatar in comments AGAIN so that we get the blavatar for pingbacks and trackbacks

= 12 May 2009 =
* fixing p2 page styles and empty title submition

= 7 May 2009 =
* fixing empty blog posting bug (thanks andy)
* fixing empty blog posting bug (thanks andy)
* fixing the odd title bug count for pages that are not the front page
* a ton of p2 changes

= 29 April 2009 =
* fixing problem with http titles
* use get_avatar in comments so that we get the blavatar for pingbacks and trackbacks

= 28 April 2009 =
* fixing titles by removing the filter and adding a seaprate title function... gahhhh
* Proper label for Prologue option checkbox
* update changelog
* making trackbacks not show up on frontpage
* more tweaks to the title function
* more tweaks to the title function
* fixing up the big title comparison function
* adding is_admin() hook to default option for titles
* seriously actually making it so that titles are on by default - thanks nick
* enabling display titles by default
* enabling display titles by default
* enabling display titles by default
* enabling display titles by default

= 27 April 2009 =
* fixing prologue title comparison algorithm
* forgot to commit the changelog
* not logging errors
* fixing the sidebar widget and reverting the revert
* fixing the sidebar widget and reverting changes... sigh
* fixing the sidebar widget and reverting changes... sigh
* small tweak for empty titles
* small tweak for the admin area to not filter titles
* fixing the big titles bug, changes in changelog

= 23 April 2009 =
* fixing margins in p2

= 22 April 2009 =
* only display title when the option is turned on

= 21 April 2009 =
* adding attachment template
* Don't stripslashes from comment content, because nobody added any. Also, it leads Barry to submitting wrong contest solutions.
* fixing a bug with titles in the recent comments widget not showing up
* new ltranslations, fixes for galleries, and titles for posts that were written in the wordpress backend

= 16 April 2009 =
* adding gpl license
* adding extended description to style.css
* adding translation abilities for the word on and giving proper strings around json class

= 15 April 2009 =
* updating style.css tags to work with wpcom
* fixing json class clash, adding japanese translations and updating changelog

= 13 April 2009 =
* iphone style updates and some code cleaning for the svn export

= 10 April 2009 =
* adding a closed window confirmation box

= 8 April 2009 =
* Attribute escaping and $wpdb->prepare() for P2
* Revert most of [1843], breaks tags in posts
* dl/dd style
* security

= 6 April 2009 =
* Add RTL tag to themes with RTL styles.

= 1 April 2009 =
* Select all so we don't pollute the cache with incomplete objects
* Acronym style for P2
* Remove generator meta tag
* fixing tabindex js

= 31 March 2009 =
* Update Bulgarian and Hebrew translations for P2
* Add rtl.css and Hebrew translation

= 30 March 2009 =
* Bust the p2.js cache
* Add POT file and Bulgarian translation for P2
* Bring the i18n patch back, without the extra added #respond divs
* Revert the i18n patch, until I fix the reply actions
* I18n for P2. Templates cleanup.

= 29 March 2009 =
* Only use the URL's #fragment if the comment is visible on the pgae.  Fix comment permalink bug

= 27 March 2009 =
* Don't bust p2.js cache on each request
* P2, first pass

= 25 March 2009 =
* clean up some code
* revert [1801] for now - looking at [1800] first
* clean up some code
* Proper escaping for P2
* fix title padding

= 23 March 2009 =
* fixing prologue title bug
* escaping some  vars and fixing default widget titles
* - Don't hard code the default gravatar to http://s.wordpress.com/i/mu.gif

= 18 March 2009 =
* Fixed author feed link.
* small
* small

= 13 March 2009 =
* fixing input css
* fixing bottom_of_entry div in prologue
* fix for styles for inline editing and hopefully fix a few bugs on inline editing

= 11 March 2009 =
* prevent scheduled posts from appearing
* update screenshot
* s/projects/tags/

= 10 March 2009 =
* fixing author pages a tad
* fixing disabled fields, so they work right
* updates to p2 to fix bugs
* fixing a mound of bugs for p2

= 9 March 2009 =
* p2 first commit
