=== Block, Suspend, Report for BuddyPress ===
Contributors: bouncingsprout
Tags: buddypress, block, suspend, report, moderation
Requires at least: 4.6
Tested up to: 6.2
Requires PHP: 7.0
Stable tag: 3.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Block, Suspend, Report for BuddyPress provides enhanced moderation for your BuddyPress or BuddyBoss site.

== Description ==

Block, Suspend, Report for BuddyPress is a must-have plugin for EVERY BuddyPress installation.

For a full list of features, documentation, screenshots and videos, head to the [plugin page](https://www.bouncingsprout.com/plugins/block-suspend-report-for-buddypress/).

Each tool is explained further below.

== Block ==

The Block tool allows your members to block another member. They simply click on a 'block' button, either on another member's profile, or on the member list. Once blocked, neither member can send a message to the other. Each member's profile pages become inaccessible, and display a simple message. Neither can view each other's activity updates, or replies, and neither can send a friend request to one another.

* A simple solution to deal with disputes between members.
* Prevents escalation of abusive or unacceptable conduct between your members.
* Lets your members deal with disputes by themselves, saving you time.

== Suspend ==

The Suspend tool allows you, as an administrator, to suspend a member. Available only to administrators is a 'suspend' button on your member's profiles, the member directory, and the admin users screen. Once clicked by you, all of that member's sessions are terminated, and they will be unable to log back in, instead seeing a custom message explaining the situation.

* Quickly deals with members who have breached your acceptable standards policies.
* Allows you to disable an account - until now, you would have to delete the account, or mark them as a spammer.
* Keeps their profile, uploads, messages and other assets ready, should you decide they can return to the site.
* See all your suspended members from the back end.

== Report ==

Flag inappropriate, abusive, or otherwise unacceptable behaviour to the site administrator. Each member's profile now incorporates a 'report' button. This creates a new report, which administrators can see in the backend. You can specify a report type, similar to social media networks such as Facebook, to designate that report as 'spam' or 'abusive' and so on. You can set Block, Suspend, Report to email you when a new report is received.

* Gives your members peace of mind that the site is open to moderation.
* Allows you to deal with any report in your own time, without having to respond to internal messages or other notifications.
* Set a threshold to automatically suspend a user when a number of reports are received.
* Mark new reports as read/unread
* Mark reports as unsubstantiated
* See number of unread reports from the menu or the dashboard

== Block, Suspend, Report for BuddyPress Premium Editions ==

= The Premium editions adds the following features: =

* Pre-approval system for new activities and group posts
* A 'Latest Reports' box in the dashboard
* A 'Most Blocked' box, showing your site's worst offenders
* Integration with Paid Memberships Pro - enable blocking and reporting for certain levels only.
* Assign a role to a suspended user
* Automatically receive new features as they are built
* Ability to add custom CSS to match your theme's styling perfectly
* Premium email support

= Plus, a super-charged report system: =

* You can allow a huge range of content to be reported and moderated, not just members themselves:

WordPress Comments
Activity Updates
Activity Comments
Groups
Private Messages
Forum Topics
Forum Replies
rtMedia Uploads

* Create your own report types, or edit the default ones.
* Add your own reports from the backend, ideal for when your members flag content directly to you via a private message or similar.
* Whitelist any user roles that cannot be reported
* Blacklist members so they can't send reports (ideal for malicious complaints)
* Quickly moderate (hide) any activity from the dashboard
* Add your own moderators who have front-end access to the reporting system
* Let your moderators deal with reports, suspensions, activity moderation

== Fully Tested With ==

* Youzify
* Beehive Theme
* [BP Better Messages](https://www.wordplus.org/downloads/bp-better-messages/)

== Not compatible with ==

* Vikinger Theme

== Screenshots ==
1. Blocked member list
2. Admin successful suspension notice
3. Send admin a custom message when a user reports another user (pro feature)

== Installation ==

Method 1:

1. Go to WordPress Dashboard->Plugins->Add New
2. Search for Block, Suspend, Report for BuddyPress using search option
3. Find the plugin and click Install Now button
4. After installation, click on Activate Plugin link to activate the plugin.

Method 2:

1. Download the plugin bp-toolkit.zip
2. Unpack the bp-toolkit.zip file and extract the bp-toolkit folder
3. Upload the plugin folder to your /wp-content/plugins/ directory
4. Go to WordPress dashboard, click on Plugins from the menu
5. Locate the bp-toolkit plugin and click on Activate link to activate the plugin.

Method 3:

1. Download the plugin bp-toolkit.zip
2. Go to WordPress Dashboard-Plugins-Add New
3. Click on Upload Plugin link from top
4. Upload the downloaded bp-toolkit.zip file and click on Install Now
5. After installation, click on Activate Plugin link to activate the plugin.

== Frequently Asked Questions ==

= Where can I find answers to any questions? =

We keep a fully updated FAQs on our [website](https://www.bouncingsprout.com/plugins/block-suspend-report-for-buddypress/#faqs). Please check there first.

If your question still isn't answered, feel free to open a support ticket from the 'Support' link above. Or, send us an email via our website.

== Changelog ==

= 3.6.1 =
* Upgrade Freemius
* Testing with WP 6.2

= 3.6.0 =
* Fix bug affecting sites with large number of users
* Bump WordPress version
* Bump Freemius version

= 3.5.0 =
* Pro edition - pre-approval system for activities and group posts
* Site moderators now correctly see moderated activities
* minor bugfixes and improvements
* Tested with WordPress 6.0.1

= 3.4.0 =

* Styling improvements
* Minor bugfixes
* Tested with WordPress 5.9.2

= 3.3.3 =
* Bump to WordPress 5.9.1
* Update Freemius library

= 3.3.2 =
* Now correctly rejects/withdraws friendship requests when a user blocks another
* Adds a filter (bsr_cancel_friendship_on_block) that tells the plugin to cancel existing friendships when a user blocks another. Defaults to false.
* Blocked Members list now shows the username next to the display name, to solve issue where users had identical display names
* Database housekeeping
* Add a block and unblock function to make it easier to extend the plugin
* Fix an untranslatable string
* Bump to WordPress 5.8.1

= 3.3.1 =
* Fix BuddyBoss activity report button styling
* Let users choose whether to display the 'report' text label in activity feeds (useful if your theme only uses icons)

= 3.3.0 =
* Fix BuddyBoss check
* Fix forum reply report link showing independently
* Fix bug in reports list
* Freemius library update
* Fixed color of menu icon
* Improve Youzify compatibility
* Site moderators can now delete activity
* Site moderators can no longer be blocked
* Fix bug where multiple emails weren't sending
* Improve button styling for Nouveau-based themes
* Add support for Beehive Theme

= 3.2.5 =
* Admins can now trash reports from the reports list
* Minor improvements
* Bump to WP 5.6
* Freemius to 2.4.1

= 3.2.4 =
* Fixed issue where administrators couldn't view rtMedia inside lightbox popups
* Add ability to create your own templates for the blocked/blocking page
* Following user feedback, the report button in the activity feed is now hidden rather than disabled where you cannot report an item
* Minor styling improvements

= 3.2.3 =
* Fixed an issue that was throwing an error when BuddyPress is deactivated

= 3.2.2 =
* Temporary fix so messages can be sent

= 3.2.1 =
* Fix issue with blog post comment reply button not showing

= 3.2.0 =
* BuddyBoss Support - add blocked members link to BuddyBoss profile menu
* Add block button to BuddyBoss member lists
* Fix report modal and overlay appearing under BuddyBoss photo lightboxes
* Move the plugin menu up the list
* Add an appropriate icon to the blocked members list when using BuddyBoss
* Fix assorted typos
* Fix shortcuts not showing in profile and admin bar menus
* Fix issue with un-friending when blocking


= 3.1.5 =
* Minor bugfixes

= 3.1.4 =
* Minor bugfixes

= 3.1.3 =
* Plugin now integrates with BuddyBoss privacy scope
* Enhancement - compatibility with BuddyBoss 'follow' system

= 3.1.2 =
* Add Blocked Members link to admin bar profile settings menu
* Fixed issue where activity filtering was breaking if a scope was provided
* Fixed bug that prevented deactivation
* Freemius update
* WordPress Version bump


= 3.1.1 =
* Fixed bug where admins weren't having capabilities added

= 3.1.0 =
* Members can no longer see activity from suspended users
* Increase PHP requirement
* Minor bug fixes
* Correct link to profile on edit report screen (props to Pär!)
* Fixed an ajax conflict with BuddyMeet (thanks to @chrisunion!)


= 3.0.4 =
* Add ability to quickly see all suspended members from the users table
* Fix issue with admin notices displaying in wrong places
* User avatars inside reports for faster identification
* Ability to edit the report title to something more memorable
* Get to reporter/reported profile from the report screen
* See number of reports, a reported member is subject to, from the reports table
* See total reports per item from the reports table
* Ability to mark reports as read or unread
* Suspended users are removed from member directories
* You can no longer see activities and comments from suspended users
* 404 page displayed if trying to access suspended member
* Improved blocked/blocking user redirection screen
* You can now block a user who has blocked you from their profile
* Activity comments are now completely hidden when you block someone
* Blocked/blocking members no longer see @mentions
* Blocked/blocking members no longer appear in suggestion lists
* Minor changes to report form UI
* Simplified admin created report form
* Ability to mark reports as substantiated
* Added various translatable strings
* Unread count in the menu
* The plugin now uses the BuddyPress native email system, taking advantage of its extensive functionality
* You now cannot make multiple reports for the same item, unless explicitly allowed. This prevents triggering of auto-moderation for the same item, by the same person
* Improvements to the Nouveau template
* Closing the report form, by clicking outside it, now resets the form as expected
* Users cannot see members blocking them in the member directory (previously one-way only)


= 2.0.5 =
* Fixed error on blocked members screen

= 2.0.4 =
* Performance improvement on report screen
* Updated support information
* Minor bug fixes


= 2.0.3 =
* Improved support for Loco Translate
* Update Freemius library
* Tested up to WP 5.3.2

= 2.0.2 =
* Improved report activity link in bp-nouveau based themes
* Remove bug where the text inside buttons was the same colour as the button background
* Fix bug that was stripping slashes out unnecessarily
* Provide support for Loco Translate
* Provide internationalization support
* Fix bug that prevents deletion of custom taxonomy items
* Improve positioning of admin notices
* Fix bug where you can only report once per page
* Add a featured news section
* Add border box style to report box
* Fix bug in report activity link where wrong activity was reported

= 2.0.1 =
* Fixed a bug that broke the layout on the Gwangi theme

= 2.0.0 =
* Major overhaul of plugin - see plugin homepage for latest features

= 1.0.5 =
* Change the hook used to construct member query

= 1.0.4 =
* Code improvements

= 1.0.3 =
* Change of name to make it easier for people to find us! (With thanks to Paul).
* Allow for ratings in the admin screen footer

= 1.0.2 =
* Add classes to buttons
* Testing with WP 5.2.2
* Add improved contact page
* Update Freemius library to 2.3.0

= 1.0.1 =
* Create a welcome screen in 'settings'
* Integration with Freemius
