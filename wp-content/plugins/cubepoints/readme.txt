=== Plugin Name ===
Contributors: lauweijie7715, petester
Donate link: http://cubepoints.com/donate/
Tags: points, comments, post, admin, widget, sidebar, paypal, gamification, rewards
Requires at least: 2.2
Tested up to: 3.4.2
Stable tag: 3.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

CubePoints is a point management system for WordPress.

== Description ==

CubePoints is a point management system for sites running on WordPress. Users can earn virtual credits on your site by posting comments, creating posts, or even by logging in each day! Install CubePoints and watch your visitor interaction soar by offering them points which could be used to view certain posts, exchange for downloads or even real items!

CubePoints is modular! And this means that it can be easily extended to offer more ways for your users to earn and spend points. APIs are also available for other plugins to work with CubePoints.

> #### CubePoints Support Forum
> Please visit the [CubePoints Support Forum](http://cubepoints.com/forums) for additional help with using this plugin. You may also post feature requests, ideas and bug reports there.

== Installation ==

Installing should be a piece of cake and take fewer than five minutes.

1. Unzip and upload upload the `cubepoints` directory to the WordPress plugin directory.
2. Activate the plugin through the 'Plugins' menu in WordPress. 
3. Configure the plugin to your liking through the 'CubePoints' menu. 
4. Add the CubePoints widget to your blog to show users the number of points they currently have.

For advanced configurations and integration with themes and other plugins, you may view the documentation that is included with the plugin.

== Frequently Asked Questions ==

= Where can I find additional help and support for this plugin? =

You may visit the [CubePoints Forum](http://cubepoints.com/forums/) for additional community help and support.

= How do I activate other features? =

Additional features can be activated from the modules page.

== Screenshots ==

1. Manage the points of your users
2. View recent point transactions
3. Extend CubePoints with modules
4. Advanced widget configurations

== Upgrade Notice ==

= 3.2.1 =
Hotfix for version 3.2. Fixes the broken "Donate" module and adds the "Limit Comment Points" that was missing from version 3.2.

= 3.2 =
New "Limit Comment Points" module added. "Donate" module updated. Several bugfixes to the "PayPal Top-up" module, "Custom Points" module and CubePoints core.

= 3.1.1 =
Translations for 11 locales added. Problems with RTL support, "PayPal Top-up" module & "Comment Spam Control" module fixed.

= 3.1 =
New features and modules added. Several bugs fixed as well.

= 3.0.3 =
New module (YouTube Video) added. Minor bugfixes made and improved code for XHTML validation.

= 3.0.2 =
New "My Points" module added will allow users to see their recent point transactions.

= 3.0.1 =
Fixed a bug with the ranks module.

= 3.0.0 =
Complete rewrite of the CubePoints plugin! This version is not tested to work with the CubePoints-BuddyPress plugin as of yet. Some core features have been removed but will be shortly added as modules.

= 2.1.3 =
Bugfix where rank images might not display properly. In addition, you can now mass-edit points in the Manage screen.

= 2.1.2 =
Minor bugfix where some sites might not load the user-info boxes from the sidebar. No update required if you are not currently facing any problems with CubePoints.

= 2.1.1 =
Comments made by guests might not show in 2.1. This is now fixed in 2.1.1

= 2.1 =
Daily Points and "My Points" page are now added in this version, as well as a lot of minor improvements/bugfixes.

= 2.0 =
Many bugs found in previous versions are now fixed in this version, including performance issues, donation problems, etc. Ranks are also added.

== Changelog ==

**Version 3.2.1** *(November 17th, 2012)*

+ [Bugfix] Fixes the broken "Donate" module in version 3.2 due to missing files
+ [Feature] "Limit Comment Points" module that was left out in version 3.2 added

**Version 3.2** *(November 16th, 2012)*

+ [Feature] New "Limit Comment Points" module added
+ [Change] Donate module updated with a new frontend interface
+ [Bugfix] Fixed a bug in the "Custom Points" module which prevented it from working
+ [Bugfix] Several bugfixes and and optimisations to the "PayPal Top-up" module
+ [Bugfix] Code that produce E_NOTICE errors fixed
+ [Bugfix] Fixes the issue where ajax requests in the admin back-end (e.g. updating points) fails to run if the administration over SSL is forced

**Version 3.1.1** *(July 13th, 2012)*

+ [Translation] Added I18n for 11 locales: Arabic (ar), German (de_DE), Spanish (es_ES), French (fr_FR), Hungarian (hu_HU), Italian (it_IT), Polish (pl_PL), Portuguese (pt_BR), Romanian (ro_RO), Russianb (ru_RU), Ukrainian (uk)
+ [Bugfix] Fixed the issue where points do not get added after a successful payment in the "PayPal Top-up" module
+ [Bugfix] Fixed RTL Language support
+ [Bugfix] Fixed problem with the "Comment Spam Control" module

**Version 3.1** *(September 29th, 2011)*

+ [Feature] Add points with custom log descriptions
+ [Feature] Updates to "Paid Content" module which allows page authors to earn points from users
+ [Feature] New "Backup and Restore" module added
+ [Feature] New "Reset" module added
+ [Change] Updates to the CubePoints modules system
+ [Bugfix] Fixed bug where the donate module would not work if unicode characters are entered in the message

**Version 3.0.3** *(May 13th, 2011)*

+ [Change] Improvement to code to improve XHTML Validation and less E_NOTICE errors when WordPress is in debug mode
+ [Feature] New "YouTube Video" module added
+ [Bugfix] Fixed a small JS bug for the module management page

**Version 3.0.2** *(April 22nd, 2011)*

+ [Feature] New "My Points" module added

**Version 3.0.1** *(April 17th, 2011)*

+ [Bugfix] Fixed a bug in the ranks module

**Version 3.0.0** *(April 16th, 2011)*

+ [Change] Complete rewrite of the CubePoints plugin! Change in database structure.
+ [Feature] Modular system introduced

**Version 2.1.3** *(May 22nd, 2010)*

+ [Bugfix] Rank Images display problem
+ [Change] Ability to mass-edit points in Manage screens

**Version 2.1.2** *(May 3rd, 2010)*

+ [Bugfix] Fixed a problem where some users might have nothing displayed in the user-info boxes

**Version 2.1.1** *(January 1st, 2010)*

+ [Bugfix] Fixed display of comments made by guests

**Version 2.1** *(January 1st, 2010)*

+ [Feature] "My Points" page
+ [Feature] "Daily" Points
+ [Feature] About Dialog Customizations
+ [Feature] Ability to Remove Rank
+ [Feature] i18n GetText Calls are now added
+ [Bugfix] Removed the "Donate" button from About Dialog when disabled
+ [Change] More JS-dynamic Donation interface

**Version 2.0** *(December 25th, 2009)*

+ [Bugfix] Complete Recode!
+ [Bugfix] Major Performance and Donation Bugfix.
+ [Feature] Ranks System added.
+ [Feature] Custom Logging types added.
+ [Change] Donation and About User screen improved.

**Version 1.3.1** *(August 3rd, 2009)*

+ [Bugfix] IMPORTANT! Registration problem

**Version 1.3** *(August 1st, 2009)*

+ [Feature] Added Cron jobs helper to automatically increase points over a period of time.
+ [Feature] Added a Module / API system.
+ [Bugfix] jQuery and javascript files are now called correctly. There should be no more conflicts with other javascript-heavy plugins.
+ [Change] Change in database structure to prepare for the shop plugin.

**Version 1.2.5b** *(April 6th, 2009)*

+ [Feature] Reset all users' points

**Version 1.2.4** *(February 5th, 2009)*

+ [Translation] Translations added for Chinese Simplefied (zh_CN).
+ [Translation] Translations added for German (de_DE).
+ [Feature] Display a list of users with the highest points from within a post using a few defined tags.
+ [Feature] Display the name and points of the top x user from within a post using a few defined tags.
+ [Change] Some strings for i18n are changed for better l10n.
+ [Bugfix] Translation now displays properly.

**Version 1.2.3** *(January 22nd, 2009)*

+ [Bugfix] Users with E_NOTICE turned on for error_reporting get the "Undefined index: q" error. 
+ [Bugfix] Users using older versions of PHP received the T_OBJECT_OPERATOR error. 
+ [Bugfix] Donation links removed from within the admin panel under comment management.
+ [Feature] Added option to prevent certain users from showing up in the Top Users widget. 

**Version 1.2.2** *(January 15th, 2009)*

+ [Bugfix] Fixed bug where users are not sorted correctly in the Top Users Widget
+ [Change] Improvements in internationalisation
+ [Change] .POT files generated

**Version 1.2.1** *(January 12th, 2009)*

+ [Bugfix] Performance issue with the Top Users Widget
+ [Change] .PO files added

**Version 1.2** *(January 11th, 2009)*

+ [Feature] A logging system has been implemented in CubePoints.
+ [Feature] New Stats Widget available! It shows the users with the highest number of points and the a count of their posts and comments.
+ [Feature] Pagination in Admin Panel -> Manage Screen. You can now browse through long lists of users easily.
+ [Feature] Added ability for logged in users to view points of other users from comments.
+ [Change] GetText functions are now implemented in CubePoints files.
+ [Bugfix] Fixed bug where users get points for updating posts. The logging feature has to be enabled to have this fix to work.
+ [Bugfix] Thickbox CSS-related problems are now fixed. (Post title does not appear correctly, etc.)
+ [Bugfix] Donate link appeared on widget even though donations are turned off.

**Version 1.1** *(January 2nd, 2009)*

+ [Change] Ajax-fy Donation with Thickbox 
+ [Feature] Ability to add points for publishing post 
+ [Feature] Ability to customize (individually) amount of points to deduct or add upon deletion/comment/publish 
+ [Change] Removed Custom CSS option in Admin Panel, moved into a seperate CSS file under /cubepoints.css 

**Version 1.0** *(December 27th, 2008)*

+ Initial Release