=== WordPress Download Manager ===
Contributors: codename065
Donate link: 
Tags: file management plugin, downloads, document management plugin, download manager, file manager, download monitor, download counter, password protection, downlad tracker, download protection
Requires at least: 3.4
Tested up to: 3.6
License: GPLv2 or later
 
  
 
This is a Files and Document Management plugin which will help you to manage, track and control file downloads from your wordpress site.
   

== Description ==
WordPress Download Manager is a Files and Document Management plugin for your WordPress Site. WordPress Download Manager plugin will help you to manage, track and control file downloads from your wordpress site. You can set password and set access level any of your downloadable files from your wordpress site.
You can add/embed downloadable files anywhere in the post just pasting the embed code inside your post content using WordPress Download Manager.

`"Download Monitor" to "Download Manager" Files Importer Integrated. Import all download monitor files in a single click`

= Features =
*	Drag and Drop File Upload
*	Control who can access to download
*	Password protection
*	Download Counter
*	Control who can user this plugin (author, editor, administrator)
*	Custom download link icon
*	File type icon support
*	DataTable support ( use short-code [wpdm_all_packages] )
*	Seatching and Sorting Option
*	Custom link label
*	Shortcode for download link
*	Shortcode for direct link to downloadable file [wpdm_hotlink id=file_id_required link_label=any_text_optional]
*	New templates for file links
*	WP Thickbox popup for download page
*	Tinymce button for short-code embed
*	Widget for new downloads
*	Multi-level Categories
*	Custom TinyMce Button
*	Category embed short-code
*	Advanced server file browser
*	Complete category and file tree using a simple short-code [wpdm_tree]
*	"Download Monitor" to "Download Manager" files Importer 

 
 
== Installation ==


1. Upload `download-manager` to the `/wp-content/plugins/`  directory
2. Activate the plugin through the 'Plugins' menu in WordPress



== Screenshots ==
1. Create new download package
2. Manage download packages
3. Categories
4. Frton-end link template preview
5. Full tree view of categories and files with a simple short-code [wpdm_tree]
6. Sortable and Searchable Download List , use short-code [wpdm_all_packages] to embed the list
7. Insert short-code
8. Create a new download pacakge quickly from popup

== Changelog ==

= 2.4.8 =
* Compatibility update for wp 3.6
* Adjusted minor css issue

= 2.4.7 =
* added title and description support to category short-code

= 2.4.6 =
* Added stripslashed for title and description
* Upgraded some intenal css

= 2.4.5 =
* Fixed the issue with tinymce button 

= 2.4.4 =
* Upgrade tinymce button feature, added quick add option

= 2.4.3 =
* Fixed issue with categories
* Fixed Download Limit Issue

= 2.4.2 =

* Added icon support for category short-code

= 2.4.1 =

* Upgraded category sort-code

= 2.4.0 =

* Fixed members download issue

= 2.3.9 =
* Fxied the issue with fread

= 2.3.8 =
* Adjusted broken file issue

= 2.3.7 =
* Upgraded new download widget

= 2.3.6 =
* Upgraded tree view

= 2.3.5 =
* Optimized ui and some internal code for better experience
* Fixed tree view short-code issue

= 2.3.4 =
* Fixed file save issue with v2.3.3
* Fixed issue with download monitor import 

= 2.3.3 =
* added search functionality in admin
* added individual icon support 
* added new short-code for all download using datatable.js, with sorting and searhing option

= 2.3.2 = 
* Fixed a minor issue with uploader

= 2.3.1 = 
* Optimized for wp 3.5
* Upgraded file upload option
* ADjusted file delete issue
* Upgraded content formatting

= 2.3.0 = 
* Fixed category pagination issue
* Fixed category count issue
* Fixed 'facebook' css class issue
* Fixed file delete option

= 2.2.9 =
* Added new short-code [wpdm_tree] to show all files and categories in tree format
* Fixed image issue with file description
* Fixed subcategory edit issue

= 2.2.8 =
* Fixed a minor database issue with file list

= 2.2.7 =
* Fixed server file browser issue

= 2.2.6 =
* Adjusted enqueue script issue

= 2.2.5 =
* Fixed compatibility issue with wordpress 3.4

= 2.2.4 =
* Fixed empty category name issue
* Added new option to delete all category
* Fixed delete category issue

= 2.2.3 =
* Fixed category page security issue

= 2.2.2 =
* setHtaccess funtion error fixed
* optimized front-end css
* additional button template added

= 2.2.1 =
* adjusted issue with template selection in tinymce popup
* hyperlink issue with description fixed
* adjusted css styling issue

= 2.2.0 =
* New templates for file links
* WP Thickbox popup for download page
* Upgraded tiny-mce butotn

= 2.1.3 =
* update short-code from {filelink=fileid} to [file id=fileid]. also support for old styles shortcode exists.

= 2.1.2 =
* fixed download issues with 2.1.1
* acitvated direct download without apearing popup for the files without password, so popup will apear only for files with password
= 2.1.1 =
* added new shortcode [wpdm_hotlink id=file_id_required link_label=any_text_optional], use the short-code to place direct download link to files without showing popup
= 2.1.0 =
* adjusted category hirarchy issue on parent selection
* download monitor importer adjusted

= 2.0.19 =
* members download issue fixed with widget

= 2.0.18 =
* members download issue fixed with category embed code

= 2.0.17 =
* server file browser issue fixed

= 2.0.16 =
* memory limit error fixed
* tinymce issue adjusted
* download url issue adjusted
* `file not found` issue adjusted

= 2.0.15 =

* pagination class conflict issue resolved
* adjusted a minor database bug

= 2.0.14 =
* Addded option for "Import Download Monitor files". You can use this option if you already using "Download Monitor" from earlier and want use "Download Manager" now. It'll import all files and categories from "Download Monitor" to "Download Manager"

= 2.0.13 =
* access option restored

= 2.0.12 =
* frontend download counter issue adjusted

= 2.0.11 =
* download counter and download label issue fixed

= 2.0.10 =
* fixed bug with server browser 
* fixed bug with db table creation

= 2.0.9 =
* added categroy feature
* new popup style added
* advanced server file browser added


= 2.0.7 =
* fixed bug with installation
* fixed bug with icon

= 2.0.6 =
* new widget added for showing new downloads
* adjusted file delete issue

= 2.0.5 =
* new option for tiny-mce button added
* "Install" function conflict resolved

= 2.0.4 =
* some plgins conflict adjusted
* new option added for setting custom message
* new option added for uploading upload link icon

= 2.0.3 =
* Add/Edit Downoad count option added
= 2.0.2 =
* database class conflick fixed

= 2.0.1 =
* New Option added for download link label

= 1.5.9 =
* Hotlink protection added

= 1.5.33 = 
* Add new option for controlling plugin access. Now you can set access level for the plugin

= 1.5.32 = 
* Minor bug fixed with creating db table

= 1.5.3 = 
* Download counter show/hide feature added for frontend download counter display

= 1.5.2 =
* Added admin option to see download counts
* 3 Minor bugs fixed


= 1.5.1 =
* Adjsuted minor issues with download counter

= 1.5 = 
* New feature: Download counter
* 2 minor bug fixed

= 1.4 =
* Fixed conflict with some other plugins

= 1.3 = 
* Fixed issue with pagination

= 1.2.5 =
* Added new option for automatic dir creation

= 1.2.4 =
* Fixed bug with upload path
* `File exists` check added
* Moved upload dir to new location for security reason

= 1.2.3 =
* removed function mime_content_type()
* Thanks Adnest (adnest@gmail.com) for your help

= 1.2.2 =
* Fixed bug with edit item


= 1.2.1 =
* Fixed bug with download link

= 1.2 =
* Fxied installation bug



= 1.1 =
* Fixed security bug with direct download protection


