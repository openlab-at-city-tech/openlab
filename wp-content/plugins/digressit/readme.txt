=== Digress.it ===
Contributors: visudo
Donate link: http://digress.it
Tags: comments, annotation, discussion, commenting, paragraph, documents, education, government
Requires at least: 2.9
Tested up to: 3.1
Stable tag: 3.1.2

Digress.it lets you comment paragraph by paragraph in the margins of a text

== Description ==

Digress.it lets you comment paragraph by paragraph in the margins of a text.

Since its initial launch, Digress.it has been used by universities, publishers and governments across the world and is cited on various academic and scientific journals as an exemplary online collaboration tool.   For some examples check out http://digress.it/examples


* <b>Support</b> - If you have any questions, there is an active mailing list at https://groups.google.com/group/digressit
* <b>Try it!</b> - If you would like to use Digress.it but do not have a hosting service - you can register for free at: http://digress.it/wp-register.php
* <b>Documentation</b> -  For more information on how to use Digress.it check out http://digress.it/help/
* <b>Extend</b> - There is now a documented API that you can reliably expand Digress.it from. More information can be found at http://digress.it/code
* <b>Want to Contribute?</b> - We are looking for help in a few different areas: Localization (support multiple languages), Documentation, JS optimization, Q&A and outreach. If you would like to help in any of these  areas, contact eddie@digress.it

== Installation ==

1. Upload `digressit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Appearance menu and review the settings. The Digress.it theme should have been automatically activated.


== Screenshots ==
* The front page of the site
* A selected paragraph with corresponding comments
* The navigation bar
* Filter through the comments using the comment browser

== Upgrade Notice == 

Please be aware that the default theme has changed considerably. If you have made customizations to version 2.x, chances are that those changes will not migrate
over to 3.x smoothly. Also be aware that some settings have changed and are automatically reset during the update. Depending on your settings, after updating, Digress.it might be automatically disabled. This is okay. Just re-enable it and you're good to go.


== Changelog ==

= 3.1.2 = 
* Security Fix: password protected posts submitted forms to wrong url

= 3.1.1 = 
* Fix issue where minified JS was not in released version

= 3.1 =
* Hide error messages when loading extensions
* Javascript is now compressed, cutting down the size of the JS download by half
* body tag now has unique id based out of blog name (in multi-site, for specific css hacks)
* Future proofing: using add_metadata API for comment paragraph number
* Adminstrators now get a warning when they do not have permalinks enabled
* Table of Contents summary is now fixed
* Comment/Content search works more reliably
* Minor CSS fixes through out


= 3.0 =
* Partial rewrite of code
* Now everything is based on WordPress 3.0+ hooks
* Support for Network Multi-site 
* Very Expandable: Hooks scattered everywhere for easy customization
* New Default theme
* New Wireframe Theme
* Support for IE7+, Safari 3+, Chrome, Firefox 3+


= 2.3.2 =
* FIXED BUG#61 fixed problem that required user to reset to default settings after install
* now if plugin fails to activate the plugin stays deactivated
* fixed linking problem with list_user function in commentbrowser
* FIXED BUG#59 removed short tags
* Tested it out on WP3.0. seems to work!


= 2.3.1 =
* FIXED: issue with fixed position boxes not appearing before scrolling

= 2.3 =
* feeds work with permalinks
* simplexml errors and supressed and html_entity_decode() is called on text to preview xml parse errors
* Added "Edit this entry" when logged in
* Home page excluded from menu
* Ajax polling now only happens when window is in focused
* Subtler highlight...text selection of highlighted text works
* minimize icons work again
* comments don't blink-appear on page load when its lots of comments
* disabling theming... instead using stylesheets
* FIXED: editing posts was not working
* ENHANCEMENT: added support for "general comments" page
* ENHANCEMENT: The search page only shows a limited number of results. 
* FIXED: commentbrowser breaks when name has a quote mark (apostrophe)
* FIXED: when there are no comments in the block, scrolling to comment area was wonkey
* ENHANCEMENT: we support parsing lists. Currently only works with OL.
* ENHANCEMENT: clearer commentbox design. New icon that gives ability to go directly to comment form
* Permalinks work in commentbrowser
* commentbrowser supports buddypress
* FIXED: issue 11 (Add paragraph and 'Comment here' links to the content RSS fe...)
* FIXED: issue 41  (Problems parsing images?) Status changed by josswinn     
* FIXED: issue 47  (BuddyPress admin bar breaks on comment view pages) 
* FIXED: issue 38  (Remove the page title from the page)
* FIXED: issue 21  (Provide a notice when comments are moderated) 
* FIXED: issue 26  (Section level feeds are cluttered with cite text) 
* FIXED: issue 32  (List items mapping to paragraphs) 
* ENHANCEMENT: adding warning when you change content and also attempt to d...) 
* ENHANCEMENT: Paragraph level feeds of post


= 2.2 =
* RSS minor switch: change "on paragraph number #9" to "(paragraph no. 9)"
* FIXED: The embed Object code was not loading properly when clicked. Now it does
* ENHANCEMENT: html embed is now a blockquote instead of div
* FIXED: alert would appear when set to classic mode
* ISSUE#4 FIXED CommentBrowser now much clearer
* ISSUE#1 FIXED empty tags generate empty selection paragraph
* ISSUE#12 FIXED typo when outputting xml in embed code
* NEW: added support for google frame when available
* NEW: stylized debug mode
* If simpleXml fails it automatically reverts to regexp parsing
* ISSUE#3 FIXED comment appearing multiple times fix
* ISSUE#8 FIXED The Comments by Section page, displays the sections in reverse order
* Removed debug code to measure function speeds
* Removed mu-plugins support. For mu-support use Plugin-Commander
* FIXED: commentcount and commenticon positioning IE6
* FIXED: highlight scales properly in IE6/7
* NEW FEATURE: added the ability to enable DIGRESS.IT with different post status for private review.
* Smaller gravatar
* New design based on Lightworld Theme called "Golden"
* Now works with different themes!!
* Scroll issue resolved on IE6
* The commenticon comment count also increments when comments updated wit ajax
* Sidebar is dynamically detected and only appears when widgets are enabled

= 2.1.7 =
* containerTable is now transparent so that the round edges on commentbox stay round over text
* jquery/utils not loaded in when js is compressed, which caused IE to fail
* positioning of commentbox relative
* better detection of previous commentpress installation to properly upgrade
* In addition to JSON, HTML, TEXT, please provide an RSS switch for each paragaph
* in digress.it.embed on line 34, the post was not being parsed with embed param set to true, which doesn't print all the comment crud
* Changed 'Comments by post' to 'Comments by section' - The Table of Contents refers to them as sections, so this would be consistent.
* Embed code changed to include link to paragraph to produce trackbacks
* resizing works a bit better. resize bars would get lost of they were set smaller than min-height of commentbox
* Fixed bug MU install which caused it to spit out a blank space before headers were ready
* support for language localization

= 2.1.6 =
* Disabled polling in IE, which caused it to break on all version. Fixed now!

= 2.1.5 =
* Text content now expands and shrinks depending on screen. commentbox box now uses percent instead of pixel to position accordingly
* removed #embed-code anchor tags, which did nothing, instead using javascript:return
* FIXED: unapproved comments appeared on the comment bubble count. now they don't
* Styling on blog titles, navigation, fontsizes made a bit cleaner
* enhanced the default and classic themes
* MAJOR: Safely upgrades from CommentPress
* FIXED: A bug in positioning when there was no skin
* FIXED: pingbacks are properly styled
* better support from chrome
* user can select what page appears in the front page
* Javascript now printed on footer for better load time
* deactivate all in MU uninstalls the mu link
* MAJOR: trackbacks now appear on the comment stream with proper paragraph
* working on better support for IE6/7 (renders properly but still has features disabled)
* paragraph and user rss feeds work when using directory based hosting

= 2.1.4 =
* FIXED: The right-floating paragraph level rss feed was breaking thread behavior

= 2.1.3 =
* pages/archives/search have a bit better styling. doesn't break sidebar
* password protected pages load safely
* removed broken stylesheets. will add later


= 2.1.2 =
* notice to join community more prominent. it appears on the "posts" page and users have options to hide or the options page, where it remains
* CommentBrowser widget is enabled by default
* Users have ability to define URL for community server. Server will be released open source when ready.
* Communication with community server now has password
* install now puts an install file in the mu-plugins folder to ensure plugin is loaded properly in MU
* FIXED: in webkit(safari) when no cookie is set fixed position did not work.
* There is now a nice fade effect when unselecting a paragraph
* There is now an option to make the sidebar can now appear on frontpage
* userfeed prints out user name in title
* new paragraph feed appears on the commentbox
* Parsing now down with XPath..which allows for more complex structures. If there is an error it reverts back to Regular Expression.
* Nested tags work now (thanks to XPath). i call force_balance_tags to make sure tags match then load into simplexml then use xpath. if xpath throws and error revert to old regexp
* FIXED: bug that prevented posts from appearing under "whole page"
* FIXED: on commentbrowser/listposts, we were not getting all posts. now limit is removed
* Archive pages look a bit better
* FIXED: empty posts no longer spit out errors. instead print a message saying the post is empty

= 2.1.1 =
* Initial release


