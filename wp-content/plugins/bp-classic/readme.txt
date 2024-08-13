=== BP Classic ===
Contributors: buddypress
Donate link: https://wordpressfoundation.org
Tags: BuddyPress, backcompat
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.8
Requires PHP: 5.6
Tested up to: 6.4
Stable tag: 1.4.0

BP Classic, a BuddyPress (12.0.0 & up) backwards compatibility add-on

== Description ==

The BP Classic add-on is being developed and maintained by the official BuddyPress development team.

It was first built to provide backwards compatibility to configs where third party BuddyPress plugin(s) or theme(s) - not ready yet for the BP Rewrites API (introduced in BuddyPress 12.0.0) - are activated.

BP Classic also includes features and templates that are fully deprecated in BuddyPress 12.0.0. Here are the ones you will now find back only once you activated the BP Classic add-on:

- The BP Legacy widgets (these were migrated as Blocks in BuddyPress 9.0.0).
- The BP Default theme.
- The BP Legacy navigation globals (`buddypress()->bp_nav` & `buddypress()->bp_options_nav`).

**NB**: although the BP Classic add-on only runs when the BuddyPress version is 12.0.0 or up, you can choose to anticipate the BuddyPress 12.0.0 upgrade by activating the add-on. In this case the add-on is in "sleep mode" and will wake up as soon as BuddyPress has been upgraded to 12.0.0.

= Join the BuddyPress community =

If you're interested in contributing to BuddyPress, we'd love to have you. Head over to the [BuddyPress Documentation](https://codex.buddypress.org/participate-and-contribute/) site to find out how you can pitch in.

Growing the BuddyPress community means better software for everyone!

== Installation ==

= Requirements =

* WordPress 5.8.
* BuddyPress **12.0.0**.

= Automatic installation =

Using the automatic installation let WordPress handles everything itself. To do an automatic install of BP Classic, log in to your WordPress dashboard, navigate to the Plugins menu. Click on the Add New link, then activate the "BuddyPress Add-ons" tab to quickly find the BP Classic plugin's card.
Once you've found the BP Classic plugin, you can view details about the latest release, such as community reviews, ratings, and description. Install the BP Classic plugin by simply pressing "Install Now".

== Frequently Asked Questions ==

= Where can I get support? =

Our community provides free support at [https://buddypress.org/support/](https://buddypress.org/support/).

= Where can I report a bug? =

Report bugs or suggest ideas at [https://github.com/buddypress/bp-classic/issues](https://github.com/buddypress/bp-classic/issues), participate to this plugin development at [https://github.com/buddypress/bp-classic/pulls](https://github.com/buddypress/bp-classic/pulls).

= Who builds the BP Classic backcompat plugin? =

The BP Classic plugin is a BuddyPress backward compatibility plugin and is free software, built by an international community of volunteers. Some contributors to BuddyPress are employed by companies that use BuddyPress, while others are consultants who offer BuddyPress-related services for hire. No one is paid by the BuddyPress project for their contributions.

If you would like to provide monetary support to the BP Classic or BuddyPress plugins, please consider a donation to the [WordPress Foundation](https://wordpressfoundation.org), or ask your favorite contributor how they prefer to have their efforts rewarded.

== Screenshots ==

1. **The settings tab to associate a WP Page with a BP Directory **

== Upgrade Notice ==

= 1.4.0 =

No specific upgrade tasks needed.

= 1.3.0 =

No specific upgrade tasks needed.

= 1.2.0 =

No specific upgrade tasks needed.

= 1.1.0 =

No specific upgrade tasks needed.

= 1.0.0 =

Initial version of the plugin, no upgrade needed.

== Changelog ==

= 1.4.0 =

- Make sure bbPress topics/replies pagination is behaving as expected with BuddyPress 12.0 & up (See [#44](https://github.com/buddypress/bp-classic/pull/44)).

= 1.3.0 =

- Switch to BP root blog when migrating directories if necessary (See [#33](https://github.com/buddypress/bp-classic/pull/33)).
- Make sure Tooltips are used in Legacy widgets (See [#35](https://github.com/buddypress/bp-classic/issues/35) & [#39](https://github.com/buddypress/bp-classic/issues/39)).
- Use a npm script to get BP Default (See [#37](https://github.com/buddypress/bp-classic/issues/37)).
- Improve how we check BP Nouveau is the current BP Template Pack in use (See [#41](https://github.com/buddypress/bp-classic/issues/41)).

= 1.2.0 =

- Avoid a type mismatch issue during the migration process (See [#27](https://github.com/buddypress/bp-classic/issues/27)).
- Only check once BuddyPress current config & version are ok (See [#28](https://github.com/buddypress/bp-classic/issues/28)).
- Make sure the migration script is run on Multisite (See [#31](https://github.com/buddypress/bp-classic/issues/31)).

= 1.1.0 =

- Make sure BP Classic is activated at the same network level than BuddyPress (See [#21](https://github.com/buddypress/bp-classic/issues/21)).
- Improve the way the themes directory is registered (See [#23](https://github.com/buddypress/bp-classic/issues/23)).

= 1.0.0 =

Initial version of the plugin.
