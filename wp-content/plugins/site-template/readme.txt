=== Site Template ===
Contributors: michael_porter
Tags: eportfolio, multiuser, wpmu, templates, education
Requires at least: 2.9.2
Tested up to: 3.1
Stable tag: 1.0

Site Template is a plugin that allows site administrators to set up blog templates for their multi-site WP installs.  

== Installation ==
- Copy directory to wp-content, go to plugins and click on "Network Activate"
- Edit wp-activate.php so that the first line reads:<br>
/** define( "WP_INSTALLING", true ); **/<br>
instead of:<br>
define( "WP_INSTALLING", true );

== Description ==
Site Template is a plugin that allows site administrators to set up "site templates" for their WPMU or wordpress 3 (with networking enabled) sites.  When a user creates a new blog, he or she will be prompted with a list of site templates to select from.  Each template specifies the theme, theme options, widgets, plugins, pages, posts, categories, links, tags, menus, and link cateogries for the new site.

Creating a site template is a two step process:

First, create a site with the appropriate theme, theme options, widgets, active plugins, pages, posts, categories, tags, links, and link categories.  This site will serve as the template, and the site's name will be the name of the template.

Second, an administrator needs to flag the site as a template.  To flag a site as a template the administrator needs to go to the admin blogs page (in wpmu) or the sites page (in wp3), find the site, and click on the "Activate Template" link in the rightmost column.

Once the template is created it will appear in the list of templates whenever a user creates a new site.  Site templates are live meaning that any changes are made to a profile's template will be reflected in future sites users create with that profile.  Administrators can change the order templates appear on the "Site Template" page by clicking on the "Site Templates" link in the "Super Admin" (wp3) or "Site Admin" (wpmu) panel.   The "Site Template" page lists all templates.  In the right most column is the number of "children" of a given template or the number of sites that have been created from the specified template.  You can see the children sites by clicking on the number. 

== Changelog ==
= .3 =
 - allows for renamning template.  If no name, error message appears.  Confirmation on update.
 - adds default template to the last row of "Site Template" screen.  Default option is anchored in the last row, but the name can change.
 - adds confirmation message when site is activated or deactivated in Sites screen.

= .31 =
 - bug fix to support site template when new users register

= .32 =
 - bug fix so that the site's e-mail address is not replaced by the e-mail address of the template's creator
 - removed "Create Blog" link because it exists in "My Sites" panel
 
= .4 =
 - bug fix so delete site works more smoothly
 - adds support for deactivating and archiving sites
 - adds support for menus
 
 = 1.0 =
  - rewrote the copy site code so it skips the API and dips directly in the database.  Should support custom content types, and fix bugs with menu creation, but not sure on either as I could not reproduce.
<<<<<<< .mine
  - added code to support templates when creating site at the same time as registering.

= 1.1 =
  - moved site template options into the sites menu on the network admin page
=======
  - added code to support templates when creating site at the same time as registering.

>>>>>>> .r387807
  
  