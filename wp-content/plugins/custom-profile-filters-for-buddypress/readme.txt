=== Custom Profile Filters for BuddyPress ===
Contributors: boonebgorges, cuny-academic-commons
Tags: buddypress, profile, filter
Requires at least: WPMu 2.7.1, BuddyPress 1.0
Tested up to: WPMu 2.9.1.1, BuddyPress 1.2
Stable tag: trunk

Allows users to take control of the way that the links in their Buddypress profiles are handled.

== Description ==

Out of the box, BuddyPress automatically turns some words and phrases in the fields of a user's profile into links that, when clicked, search the user's community for other profiles containing those phrases. When activated, this plugin allows users and administrators to have more control over these links, in the following ways:

1) By using square brackets in a profile field, users can specify which words or phrases in their profile turn into links. For example: under Interests, I might list "Cartoons about dogs". By default, Buddypress will turn the entire phrase into a link that searches the community for others who like cartoons about dogs. If I instead type "[Cartoons] about [dogs]", then the two words in brackets will turn into independent links.

2) Administrators can specify certain profile fields that will not turn into links at all. The standard setting for the plugin is that fields labeled 'Phone', 'IM', and 'Skype ID' will not become linkable (it doesn't make much sense to search a community for what should be a unique handle, after all). See custom-profile-filters-for-buddypress.php to configure this setting.

3) Administrators can specify certain profile fields that link to social networking profiles. If I enter my Twitter handle 'boonebgorges' into a field labeled 'Twitter', for example, this plugin will bypass the default link to a BuddyPress search on 'boonebgorges' and instead link to http://twitter.com/boonebgorges. See custom-profile-filters-for-buddypress-bp-functions.php to configure this setting.


This plugin was created as part of the CUNY Academic Commons of the City University of New York. See http://commons.gc.cuny.edu to learn more about this bodacious project.


== Installation ==

1. Upload the `custom-profile-filters-for-buddypress` directory to your plugins folder
1. Activate the plugin
1. Edit custom-profile-filters-for-buddypress.php to configure



== Notes ==

The plugin checks each profile for square brackets and activates if it finds any. If no square brackets are found, the default automatic filter will kick in.

You might want to insert a small explanation into your BP profile edit template (/wp-content/bp-themes/[your-member-theme]/profile/edit.php that tells your site's users how to use these brackets. Here's what I use: 
	
"Words or phrases in your profile can be linked to the profiles of other members that contain the same phrases. To specify which words or phrases should be linked, add square brackets: e.g. "I enjoy [English literature] and [technology]." If you do not specify anything, phrases will be chosen automatically."

Future features include: admin tab with toggle switch; ability to tweak BP's automatic profile filter (e.g. to parse semi-colon separated lists in addition to commas).

== Changelog ==

= 0.3 =
* Conforms to BP 1.2 standards for loading order
* Most functionality moved to proper filters in order to inherit BP native code

= 0.3.1 =
* Moved globals back to main plugin file
* Fixed error regarding missing function arguments (thanks for reporting them, Mike!)
