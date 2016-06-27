=== BuddyPress Event Organiser ===
Contributors: needle
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PZSKM8T5ZP3SC
Tags: buddypress, event, organiser, groups
Requires at least: 3.6
Tested up to: 3.6.1
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin for assigning Event Organiser plugin Events to BuddyPress Groups, including support for Group Hierarchies. Each group is provided with a calendar page featuring the events assigned to it.


== Description ==

A WordPress plugin for assigning Event Organiser plugin Events to BuddyPress Groups, including support for Group Hierarchies. Each group is provided with a calendar page featuring the events assigned to it.

This plugin has been developed using WordPress 3.6, BuddyPress 1.8. It requires Event Organiser version 2.3 or greater.

NOTE: This plugin does not fully support Event Organiser calendar shortcodes at present, so any calendars that are invoked with `do_shortcode()` need a call to `_eventorganiser_delete_calendar_cache()` beforehand. 


== Installation ==

1. Extract the plugin archive 
1. Upload plugin files to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==

See https://github.com/christianwach/bp-event-organiser/commits/master