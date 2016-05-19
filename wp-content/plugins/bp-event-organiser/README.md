BuddyPress Event Organiser
==========================

A *WordPress* plugin for assigning *Event Organiser* plugin Events to *BuddyPress* Groups, including support for Group Hierarchies. Each group is provided with a calendar page featuring the events assigned to it.

#### Notes ####

This plugin has been developed using *WordPress 3.6+* and *BuddyPress 1.8+*. It requires [Event Organiser](http://wordpress.org/plugins/event-organiser/) version 2.3 or greater.

This plugin does not fully support Event Organiser calendar shortcodes at present, so any calendars that are invoked with `do_shortcode()` need a call to `_eventorganiser_delete_calendar_cache()` beforehand.

#### Installation ####

There are two ways to install from GitHub:

###### ZIP Download ######

If you have downloaded *BuddyPress Event Organiser* as a ZIP file from the GitHub repository, do the following to install and activate the plugin and theme:

1. Unzip the .zip file and, if needed, rename the enclosing folder so that the plugin's files are located directly inside `/wp-content/plugins/bp-event-organiser`
2. Activate the plugin
3. You are done, start adding events to your groups!

###### git clone ######

If you have cloned the code from GitHub, it is assumed that you know what you're doing.
