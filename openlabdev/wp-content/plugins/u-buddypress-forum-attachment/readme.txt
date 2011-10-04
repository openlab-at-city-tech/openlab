=== U BuddyPress Forum Attachment ===
Contributors: taehan
Donate link: 
Tags: BuddyPress, attachment, upload, file, forum
Requires at least: WordPress 3.1.0, BuddyPress 1.2.9
Tested up to: 3.2.1
Stable tag: 1.2.1

This plugin allows members to upload files on BuddyPress forum.

== Description ==

This plugin allows members to upload files on BuddyPress forum. Uploader is Ajax-based.

= Setting Options =
* enable/disable
* upload directory
* Max file size per file
* Max file count per post
* Upload File types
* File manager

This plugin was tested with 'bp-default' theme. if you are using other theme and file uploader is not shown, check your theme whether it has below hooks.

* 'bp_after_group_forum_post_new' (your-theme/groups/single/forum.php)
* 'groups_forum_new_reply_after' (your-theme/groups/single/forum/topic.php)
* 'groups_forum_new_topic_after' (your-theme/forums/index.php)
* 'bp_group_after_edit_forum_topic' (your-theme/groups/single/forum/edit.php)
* 'bp_group_after_edit_forum_post' (your-theme/groups/single/forum/edit.php)

If anything does not work please leave a comment at
http://urlless.com/u-buddypress-forum-attachment/

* Relevant plugin: http://wordpress.org/extend/plugins/u-buddypress-forum-editor/

== Installation ==
1. Upload <code>u-buddypress-forum-attachment</code> folder to the <code>/wp-content/plugins</code> directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the options panel under the 'BuddyPress' menu and set 'Enable' checking and set the settings you want.

== Frequently Asked Questions ==

== Screenshots ==

1. Uploader in action.
2. Settings.
3. File manager.

== Changelog ==

= 1.2.1 =
* Fixed: Filelist CSS
* Removed: Developer's information
* Added: Russian translation courtesy of Gr1N

= 1.2 =
* Added: File manager
* Added: Removable unattached file on front-end with AJAX
* Added: Create thumbnail(100x100)
* Changed: Front-end filelist design

= 1.1.2 =
* Fixed: IMPORTANT security issue with download

= 1.1.1 =
* Changed: up/download url
* Changed: filelist design

= 1.1 =
* Changed: From Flash uploader to Ajax uploader
* Added: setting options
* Removed: Form validator

= 1.0.2 =
* Fixed: Capability bug fixed on Multi-Site.

= 1.0.1 =
* Fixed: default path and url option bugs are fixed on Window OS

= 1.0 =
* Initial release.