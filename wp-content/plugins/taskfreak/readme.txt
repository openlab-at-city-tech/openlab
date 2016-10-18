=== [UNMAINTAINED] TaskFreak! Free ===
Contributors: taskfreak, HerveRenault
Donate link: http://www.taskfreak.com/
Tags: projects, tasks, management, todo, list, GTD, manage projects, project management, task management, team, planning, tracking
Requires at least: 3.3
Tested up to: 4.2.1
Stable tag: 1.0.19
Version: 1.0.19
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sorry, this plugin is no longer maintained.

== Description ==

** THIS PLUGIN IS NO LONGER MAINTAINED **

Developers can fork it on GitHub https://github.com/taskfreak/tfwp/

Manage tasks for yourself or within a group, company, organization, etc.
Create projects or teams, then add tasks and assign to users.
Add attachements, discuss tasks and follow their status.

WordPress version of the standalone [TaskFreak!](http://taskfreak.com/) web application.

Available in Arabic, English, French, German, Italian, Polish, Portuguese, Russian, Spanish.
(thanks to contributors)

Advantages :

- Easy to install
- Full integration with Wordpress users and roles
- Create public and private projects
- Add attachments and comment your tasks
- Mobile devices friendly (smartphones, tablets)
- Integrates seamlessly with your WP theme
- Users are associated to projects by WP roles

[vimeo http://vimeo.com/77401609]

== Installation ==

** THIS PLUGIN IS NO LONGER MAINTAINED **

Developers can fork it on GitHub https://github.com/taskfreak/tfwp/

1. Upload `taskfreak.zip` to the `/wp-content/plugins/` directory
2. Unzip taskfreak (should create taskfreak/ folder within plugins/)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Insert shortcode `[tfk_all]` in any page or post (or insert `<?php do_action('tfk_all'); ?>` in your template)
5. Create at least one project through the 'TaskFreak!' menu in WordPress dashboard.

Note: if your theme inserts unwanted line breaks in TaskFreak's output, then use this shortcode combination: `[raw][tfk_all][/raw]`

== Frequently Asked Questions ==

** THIS PLUGIN IS NO LONGER MAINTAINED **

Developers can fork it on GitHub https://github.com/taskfreak/tfwp/

= Where can I get some help ? =

Go to http://www.taskfreak.com/wordpress-plugin/ for more information.

= Can I get professionnal support =

If you want personnal support or have specific requests, please go to
http://www.taskfreak.com and see details about the pro version

== Screenshots ==

** THIS PLUGIN IS NO LONGER MAINTAINED **

Developers can fork it on GitHub https://github.com/taskfreak/tfwp/

1. After installing the plugin, go to the 'TaskFreak!' menu
2. Create your first project
3. Set project title, description (optional), status (or leave in 'Draft'), access rights
4. Insert the `[tfk_all]` shortcode in a page or post
5. View your page (or post)
6. Your project contains no task, time to create one: click 'New Task'
7. Enter title, set priority (1 to 3), deadline (optional), assign the task (optional), set status, ...
8. Here's what your task list looks like if you enable the 'Proximity bar' in TaskFreak! settings
9. View updates (depends on access rights for the viewer)
10. Settings

== Changelog ==

** THIS PLUGIN IS NO LONGER MAINTAINED **

Developers can fork it on GitHub https://github.com/taskfreak/tfwp/

= 1.0.18 =
Security fix (XSS)
See http://arstechnica.com/security/2015/04/21/swarm-of-wordpress-plugins-susceptible-to-potentially-dangerous-exploits/

= 1.0.17 =
Improved way of retrieving status via Ajax (no impact on users)
Russian translation thanks to Алексей Моцык (Aleksei Motsyk)

= 1.0.16 =
Added Arabic translation thanks to Ahmed Ghanem https://twitter.com/A91G

= 1.0.15 =
Added: Spanish translation thanks to Jose Miranda http://www.webboricua.com
Bug fix: missing charset collation in install (thanks to ViseirA)
Bug fix: strict standards notice with E_STRICT error reporting enabled (thanks to sirmacik)
Bug fix: improper enqueuing prevented MinQueue plugin from concatenating styles.

= 1.0.14 =
Bug fix: URL of localized datepickers changed + remove http(s) and let the browser use the proper protocol.
Thanks becki for advising us!

= 1.0.13 =
Bug fix: in the dashboard, the "Start working" link could contain unwanted parameters after changing task status.
Documentation: Some themes insert unwanted line breaks. In this case, use this shortcode combination `[raw][tfk_all][/raw]`

= 1.0.12 =
Bug fix for multisite setup.

= 1.0.11 =
Replaced the "Show/Hide History" link by an icon for better compatibility with the 2014 theme.
Force reload if it does not show up after the update.

= 1.0.10 =
Italian translation provided by Giorgio Draghetti <giorgio@tipinoncomuni.it>

= 1.0.9 =
Polish (Poland) translation provided by tretos53@github (adam.g@outlook.com)

= 1.0.8 =
Portuguese (Brazil) translation provided by Theodoro Caliari <theodorocaliari@users.noreply.github.com>

= 1.0.7 =
Fixed uninstall script.

= 1.0.6 =
German translation provided by Ian Diggance <office@interpres.de>

= 1.0.5 =
Bugfix : Removed unused unprefixed dbg() function which clashes with WYSIJA as notified by users time4novelty and SeanBanksBliss (thank you)

= 1.0.4 =
Bugfix : Details section was a bit too wide, which triggered scrollbars on "overflow: auto" containers

= 1.0.3 =
Bugfix : Added missing french translation for "No project found"

= 1.0.2 =
Added link to video in the readme

= 1.0.1 =
Added screenshots

= 1.0 =
Initial version
