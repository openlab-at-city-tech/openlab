=== Plugin Name ===
Contributors: boonebgorges, cuny-academic-commons
Tags: buddypress, groups, members, manage
Requires at least: WP 2.8, BuddyPress 1.2
Tested up to: WP 3.1, BuddyPress 1.2.8
Donate link: http://teleogistic.net/donate/
Stable tag: 0.4.3

Allows site administrators to manage BuddyPress group membership

== Description ==

This plugin creates an admin panel at Dashboard > BuddyPress > Group Management. On this panel, site admins can manage BP group membership by banning, unbanning, promoting and demoting current members of any group, adding members to any group, and deleting groups.

== Installation ==

* Upload and activate on your BuddyPress blog

== Translation credits ==

* Italian: Luca Camellini
* Turkish: gk
* German: Tom
* Dutch: [Anja](http://werkgroepen.net/wordpress/)

== Changelog ==
  
= 0.4.3 =
* Fixes bp_gm_member_action hook location so it fires properly.

= 0.4.2 =
* Compatibility with WP 3.1 Network Admin

= 0.4.1 =
* Added group type (Public/Private/Hidden) to group listing table
* Added missing gettext calls
* Added Dutch translation (thanks, Anja!)
  
= 0.4 =
* Added plugin settings page
* Member list is paginated (defaults to 50)
* Admin can now specify how many groups/members to show per page
* Group Actions menu added to Members and Delete pages
* Links to Group Admin page added
  
= 0.3.1 =
* Turkish translation added (thanks, gk!)
* German translation added (thanks, Tom!)
* Sitewide roster altered to include all members, not just members active on BP

= 0.3 = 
* Pagination on group list allows you to see all groups (thanks, Andy!)
* Group avatars added (thanks, Andy!)
* Italian translation included (thanks, Luca!)
  
= 0.2 =
* Fully localizable
* Avatar bug fixed (thanks for pointing it out, anointed!)
* Hooks added for additional group actions
  
= 0.1 =
* Initial release
